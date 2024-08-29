@extends('backend.layouts.app')

@section('title', __('Announcements'))

@section('content')
    <div>
        <x-backend.card>
            <x-slot name="header">
                Announcements
            </x-slot>

            @if (1)
                <x-slot name="headerActions">
                    <x-utils.link icon="c-icon cil-plus" class="card-header-action" :href="route('dashboard.announcements.create')" :text="__('Create Announcement')">
                    </x-utils.link>
                </x-slot>
            @endif

            <x-slot name="body">

                @if (session('Success'))
                    <div class="alert alert-success">
                        {{ session('Success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <livewire:backend.announcement-table />
            </x-slot>
        </x-backend.card>
    </div>
@endsection
