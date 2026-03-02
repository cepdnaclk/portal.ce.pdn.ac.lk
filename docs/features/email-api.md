# Email Service

## Introduction

The email service lets internal systems send emails via a Portal App and API key. Each request is authenticated with an API key, rate-limited, and recorded in delivery logs. Messages are queued for async delivery, and metadata can be stored alongside each send for auditing or correlation.

## How to create a Portal App

Portal Apps are managed from the backend:

- Navigate to **Services > Apps > App Management** (`dashboard.services.apps`).
- Use **Create New App** to save the app name.
- The app is created in `portal_apps` with an `active` or `revoked` status (active by default).

## How to create an API key

API keys are scoped to a Portal App:

- From **Services > Apps**, open the app and select **Keys** (`dashboard.services.apps.keys`).
- Use **Generate New API Key** and optionally set an expiry date.
- Copy the key immediately. It is only shown once.

## How to send an Email

Send email via the API using the API key in the `X-API-KEY` header.

Endpoint:

- `POST /api/email/v1/send`

Required payload fields:

- `to` (array of email addresses, at least one)
- `subject` (string)
- `body` (string)

Optional payload fields:

- `cc` (array of email addresses)
- `bcc` (array of email addresses)
- `reply_to` (email address)
- `template_data` (array)
- `metadata` (object for structured tracking data)

Notes:

- The request is validated against a per-message recipient limit (`EMAIL_SERVICE_MAX_RECIPIENTS`).
- Emails are queued and return a `message_id` immediately.
- `metadata` is stored with the delivery log and returned by the history endpoint so you can persist structured identifiers (ticket IDs, tenant IDs, workflow steps, etc.).

Example request:

```bash
curl -X POST "https://portal.ce.pdn.ac.lk/api/email/v1/send" \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: <your-api-key>" \
  -d '{
    "to": ["user@example.com"],
    "subject": "Welcome",
    "body": "Your account is ready.",
    "metadata": {"tenant": "engineering", "request_id": "abc-123"}
  }'
```

## How to see the email logs

You can view logs in two ways:

- **Backend UI**: **Services > Email Service > History**: lists all delivery logs with filters by Portal App and status.
- **API**: `GET /api/email/v1/history` returns logs for the Portal App bound to the API key. Optional query params: `status`, `from_date`, `to_date`, `limit`, and `page`.

## Access Control

Backend UI access:

- Portal Apps & API keys require `user.access.services.apps`.
- Email history requires `user.access.services.email`.

API access:

- Requests require the `X-API-KEY` header.
- Keys are rejected if revoked or expired.
- The Portal App must be `active`.
- The email API is rate-limited via the `email-service` limiter (60 emails per minute).

## Email sender configuration

Email delivery uses Laravel's mail configuration. Set the mailer credentials in `.env`:

- `MAIL_MAILER`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_ENCRYPTION`
- `MAIL_FROM_ADDRESS`
- `MAIL_FROM_NAME`

Email-service defaults are defined in `config/email-service.php`. The most commonly used overrides are:

- `EMAIL_SERVICE_FROM` (fallback sender)
- `EMAIL_SERVICE_QUEUE` (queue name)
- `EMAIL_SERVICE_RATE_LIMIT`
- `EMAIL_SERVICE_MAX_RECIPIENTS`
- `EMAIL_SERVICE_TEMPLATE`

## OpenAPI schema

The OpenAPI definition for the email service is available at `docs/api/email.json`. See the schema at [../api/email.json](../api/email.json).
