<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $news->title }} </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container vh-75">
        <div class="container  col-sm-12 col-md-10 col-lg-10 mx-auto mt-3 py-2">
            <span class="h3 pb-2">{{ $news->title }}</span><br>
            @if($news->published_at)
            <span class="text-muted">{{ $news->published_at }} &middot; </span>
            @endif

            @if($news->user)
            <span class="text-muted">
                {{ $news->user->name }} &middot;
            </span>
            @endif
            <hr>

            @if($news->image)
            <div class="row py-5">
                <div class="col-8 mx-auto">
                    <img src="{{ URL::to($news->thumbURL())}}" alt="{{ $news->title }}" class="img-fluid img-thumbnail">
                </div>
            </div>
            @endif
            <div class="row pt-3">
                {!! $news->description !!}
            </div>

            @if($news->link_url)
            <div class="project-section mb-5">
                <h4 class="project-section-title">Resources / Links</h4>
                <div class="container px-4">
                    <div class="pb-1">
                        <i class="fa fa-link me-2" aria-hidden="true"></i> <a href="{{ $news->link_url }}" target='_blank'>{{ $news->link_caption }}</a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</body>