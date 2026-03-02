<?php

namespace App\Policies;

use App\Domains\Email\Models\EmailDeliveryLog;
use App\Domains\Email\Models\PortalApp;

class EmailDeliveryLogPolicy
{
  public function viewForPortalApp(PortalApp $portalApp, EmailDeliveryLog $log): bool
  {
    return $log->portal_app_id === $portalApp->id;
  }

  public function viewAnyForPortalApp(PortalApp $portalApp): bool
  {
    return $portalApp->status === PortalApp::STATUS_ACTIVE;
  }
}
