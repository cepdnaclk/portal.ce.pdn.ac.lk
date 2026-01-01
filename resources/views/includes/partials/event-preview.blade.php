<div class="alert alert-warning pt-1 pb-1 mb-0">
    @lang('This is a preview of the event, :title.', ['title' => $event->title])
    <a href="{{ route('dashboard.event.index') }}">@lang('Back')</a> |
    <a href="{{ route('dashboard.event.edit', $event->id) }}">@lang('Edit')</a>
</div>
