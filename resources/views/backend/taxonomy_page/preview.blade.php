<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $taxonomyPage->slug }} </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="alert alert-warning pt-1 pb-1 mb-0">
        @lang('This is a preview of the taxonomy page, <i>:title</i>.', ['title' => $taxonomyPage->slug])
        <a href="{{ route('dashboard.taxonomy-pages.index') }}">@lang('Back')</a> |
        <a href="{{ route('dashboard.taxonomy-pages.edit', $taxonomyPage->id) }}">@lang('Edit')</a>
    </div>

    <div class="container vh-75 mt-5">
        <h4>{{ $taxonomyPage->slug }}</h4>
        <div class="border p-3">
            {!! $taxonomyPage->html !!}
        </div>
    </div>
</body>
