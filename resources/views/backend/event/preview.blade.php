<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $event->title }} </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container vh-75">
        <div class="container  col-sm-12 col-md-10 col-lg-10 mx-auto mt-3 py-2">

            <span class="h3 pb-2">{{ $event->title }}</span><br>
            @if($event->published_at)
            <span class="text-muted">{{ $event->published_at }} &middot; </span>
            @endif

            @if($event->user)
            <span class="text-muted">
                {{ $event->user->name }} &middot;
            </span>
            @endif

            <hr>

            @if($event->image)
            <div class="row py-5">
                <div class="col-8 mx-auto">
                    <img src="{{ URL::to($event->thumbURL())}}" alt="{{ $event->title }}" class="img-fluid img-thumbnail">
                </div>
            </div>
            @endif
            <div class="row pt-3">
                {!! $event->description !!}
            </div>

        </div>
    </div>
</body>