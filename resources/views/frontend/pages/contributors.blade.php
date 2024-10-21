@extends('frontend.layouts.app')

@section('title', __('Contributors'))

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                @foreach ($data as $key => $project)
                    <x-frontend.card>
                        <x-slot name="body">

                            <h3>#{{ $key + 1 }} {{ $project['title'] }}</h3>
                            <p>{{ $project['description'] }}</p>

                            <ul>
                                <li><a target="_blank" href="{{ $project['project_url'] }}">Project Page</a></li>
                                <li><a target="_blank" href="{{ $project['page_url'] }}">Project Details / Blog</a></li>
                            </ul>

                            <h3 class="pt-3">Project Team</h3>
                            <div class="row" id="teamCards">
                                @foreach ($project['team'] as $teamMember)
                                    <div class="col-4 col-sm-3 col-md-2 col-lg-2 d-flex" style="padding: 3px;">
                                        <div class="card p-1 flex-fill">
                                            <div class="overflow-hidden">
                                                <img class="card-img-top img-fluid" src="{{ $teamMember['profile_image'] }}"
                                                    alt="{{ $teamMember['name'] }}">
                                            </div>
                                            <div class="card-body p-0 d-flex flex-column">
                                                <h4 class="profile-title card-title text-center pt-1 text-wrap">
                                                    {{ $teamMember['name'] }}
                                                </h4>

                                                @if ($teamMember['profile_url'])
                                                    <div class="d-grid mt-auto px-2 pb-2">
                                                        <a href="{{ $teamMember['profile_url'] }}" target="_blank"
                                                            class="btn btn-sm btn-primary btn-block">Profile</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <h3 class="pt-3">Project Supervisors</h3>
                            <div class="row" id="supervisorCards">
                                @foreach ($project['supervisors'] as $supervisor)
                                    <div class="col-4 col-sm-3 col-md-2 col-lg-2 d-flex" style="padding: 3px;">
                                        <div class="card p-1 flex-fill">
                                            <div class="overflow-hidden">
                                                <img class="card-img-top img-fluid" src="{{ $supervisor['profile_image'] }}"
                                                    alt="{{ $supervisor['name'] }}">
                                            </div>
                                            <div class="card-body p-0 d-flex flex-column">
                                                <h4 class="profile-title card-title text-center pt-1 text-wrap">
                                                    {{ $supervisor['name'] }}
                                                </h4>

                                                @if ($supervisor['profile_url'])
                                                    <div class="d-grid mt-auto px-2 pb-2">
                                                        <a href="{{ $supervisor['profile_url'] }}" target="_blank"
                                                            class="btn btn-sm btn-primary btn-block">Profile</a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </x-slot>
                    </x-frontend.card>
                    <br />
                @endforeach
            </div>
        </div>
    </div>
@endsection
