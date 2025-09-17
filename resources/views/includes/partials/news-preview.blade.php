<div class="alert alert-warning pt-1 pb-1 mb-0">
    @lang('This is a preview of the news, :title.', ['title' => $news->title]) 
    <a href="{{ route('dashboard.news.index') }}">@lang('Back')</a> | 
    <a href="{{ route('dashboard.news.edit', $news->id) }}">@lang('Edit')</a>
</div>