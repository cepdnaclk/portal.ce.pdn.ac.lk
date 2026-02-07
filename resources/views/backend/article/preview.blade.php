<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $article->title }} </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    @include('includes.partials.article-preview')
    <div class="container vh-75">
        <div class="container col-sm-12 col-md-10 col-lg-10 mx-auto mt-3 py-2">
            <span class="h3 pb-2">{{ $article->title }}</span><br>
            @if ($article->published_at)
                <span class="text-muted">{{ $article->published_at }} &middot; </span>
            @endif

            @if ($article->author)
                <span class="text-muted">
                    {{ $article->author->name }}
                    @if ($article->author->email)
                        ({{ $article->author->email }})
                    @endif
                    &middot;
                </span>
            @endif

            @if ($article->content)
                @php
                    $words = str_word_count(strip_tags($article->content));
                    $read_time = ceil($words / 200);
                @endphp
                <span class="text-muted">
                    {{ $read_time }} mins read
                </span>
            @endif

            <hr>
            <div class="row pt-3">
                {!! $article->content !!}
            </div>
        </div>
    </div>
</body>
