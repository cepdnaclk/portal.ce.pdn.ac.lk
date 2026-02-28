@component('mail::message')

# {{ $title ?? ($subject ?? __('Notification')) }}

@isset($body)
{!! nl2br($body) ?? '' !!}
@endisset

@isset($body_markdown)
{{ Illuminate\Mail\Markdown::parse($body_markdown) }}
@endisset

@endcomponent
