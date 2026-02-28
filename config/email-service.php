<?php

return [
  // Maximum number of emails allowed to send per minute
  'rate_limit_per_minute' => env('EMAIL_SERVICE_RATE_LIMIT', 60),

  // Maximum number of recipients per single email
  'max_recipients' => env('EMAIL_SERVICE_MAX_RECIPIENTS', 50),

  // Queue name for processing emails asynchronously
  'queue' => env('EMAIL_SERVICE_QUEUE', null),

  // Default sender email address
  'default_from' => env('EMAIL_SERVICE_FROM', env('MAIL_FROM_ADDRESS', 'portal@ce.pdn.ac.lk')),

  // Default Blade template path for email layouts
  'default_template' => env('EMAIL_SERVICE_TEMPLATE', 'backend.email-service.templates.default'),

  // Support email contact for system notifications
  'support_email' => env('EMAIL_SERVICE_SUPPORT_EMAIL', 'webmaster.github.ce@eng.pdn.ac.lk')
];