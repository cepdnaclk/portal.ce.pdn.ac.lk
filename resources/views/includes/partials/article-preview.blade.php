<div class="alert alert-warning pt-1 pb-1 mb-0">
    @lang('This is a preview of the article, :title.', ['title' => $article->title])
    <a href="{{ route('dashboard.article.index') }}">@lang('Back')</a> |
    <a href="{{ route('dashboard.article.edit', $article->id) }}">@lang('Edit')</a>
</div>
