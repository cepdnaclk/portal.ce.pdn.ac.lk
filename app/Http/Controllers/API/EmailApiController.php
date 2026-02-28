<?php

namespace App\Http\Controllers\API;

use App\Domains\Email\Models\EmailDeliveryLog;
use App\Domains\Email\Models\PortalApp;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Email\SendEmailRequest;
use App\Http\Resources\EmailDeliveryLogResource;
use App\Jobs\SendEmailJob;
use App\Policies\EmailDeliveryLogPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailApiController extends Controller
{
  public function send(SendEmailRequest $request)
  {
    /** @var \App\Domains\Email\Models\ApiKey $apiKey */
    $apiKey = $request->attributes->get('apiKey');
    /** @var PortalApp $portalApp */
    $portalApp = $request->attributes->get('portalApp');

    $policy = app(EmailDeliveryLogPolicy::class);
    if (!$policy->viewAnyForPortalApp($portalApp)) {
      return response()->json(['message' => 'Portal app inactive.'], 403);
    }

    $payload = [
      'to' => $request->input('to', []),
      'cc' => $request->input('cc', []),
      'bcc' => $request->input('bcc', []),
      'reply_to' => $request->input('reply_to'),
      'subject' => $request->input('subject'),
      'template_data' => $request->input('template_data', []),
      'body' => $request->input('body'),
      'metadata' => $request->input('metadata', []),
      'from' => config('email-service.default_from') ?: 'no-reply@portal.ce.pdn.ac.lk',
    ];

    $log = EmailDeliveryLog::create([
      'portal_app_id' => $portalApp->id,
      'api_key_id' => $apiKey->id,
      'from' => $payload['from'],
      'to' => $payload['to'],
      'cc' => $payload['cc'],
      'bcc' => $payload['bcc'],
      'subject' => $payload['subject'],
      'template' => config('email-service.default_template', 'emails.default'),
      'metadata' => $payload['metadata'],
      'status' => EmailDeliveryLog::STATUS_QUEUED,
    ]);

    SendEmailJob::dispatch($log->id, $payload)->onQueue(config('email-service.queue'));

    Log::info('Email send queued', [
      'message_id' => $log->id,
      'portal_app_id' => $portalApp->id,
      'api_key_id' => $apiKey->id,
    ]);

    return response()->json([
      'message_id' => $log->id,
      'status' => $log->status,
    ]);
  }

  public function history(Request $request)
  {
    /** @var PortalApp $portalApp */
    $portalApp = $request->attributes->get('portalApp');

    $policy = app(EmailDeliveryLogPolicy::class);
    if (!$policy->viewAnyForPortalApp($portalApp)) {
      return response()->json(['message' => 'Portal app inactive.'], 403);
    }

    $query = EmailDeliveryLog::query()->forPortalApp($portalApp);

    if ($request->filled('status')) {
      $query->where('status', $request->input('status'));
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->input('to_date'));
    }

    $limit = (int) $request->input('limit', 20);
    $limit = max(1, min($limit, 100));

    $logs = $query->orderByDesc('created_at')->paginate($limit);

    return response()->json([
      'data' => EmailDeliveryLogResource::collection($logs)->resolve(),
      'pagination' => [
        'page' => $logs->currentPage(),
        'limit' => $logs->perPage(),
        'total' => $logs->total(),
      ],
    ]);
  }
}