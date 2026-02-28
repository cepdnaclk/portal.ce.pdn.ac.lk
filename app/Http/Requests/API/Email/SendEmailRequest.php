<?php

namespace App\Http\Requests\API\Email;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class SendEmailRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    return [
      'to' => ['required', 'array', 'min:1'],
      'to.*' => ['email'],
      'cc' => ['nullable', 'array'],
      'cc.*' => ['email'],
      'bcc' => ['nullable', 'array'],
      'bcc.*' => ['email'],
      'reply_to' => ['nullable', 'email'],
      'subject' => ['required', 'string', 'max:255'],
      'template_data' => ['nullable', 'array'],
      'body' => ['required', 'string'],
      'metadata' => ['nullable', 'array'],
    ];
  }

  public function withValidator(Validator $validator)
  {
    $validator->after(function (Validator $validator) {
      $maxRecipients = (int) config('email-service.max_recipients', 50);
      $recipientCount = count($this->input('to', []))
        + count($this->input('cc', []))
        + count($this->input('bcc', []));

      if ($recipientCount > $maxRecipients) {
        $validator->errors()->add('to', "Recipient limit exceeded (max {$maxRecipients}).");
      }
    });
  }
}