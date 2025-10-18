@extends('backend.layouts.app')

@section('title', __('Manage Taxonomy List'))

@section('content')
    {!! Form::model($taxonomyList, [
        'url' => route('dashboard.taxonomy-lists.update_list', $taxonomyList),
        'method' => 'PUT',
        'class' => 'container',
    ]) !!}

    @csrf

    <x-backend.card>
        <x-slot name="header">
            Taxonomy List : Manage
        </x-slot>

        <x-slot name="body">
            <div class="card">
                <div class="card-body">
                    <div class="row pt-3">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $taxonomyList->name }}</dd>

                        <dt class="col-sm-3">Taxonomy</dt>
                        <dd class="col-sm-9">{{ $taxonomyList->taxonomy?->name ?? '—' }}</dd>

                        <dt class="col-sm-3">Data Type</dt>
                        <dd class="col-sm-9">
                            {{ $taxonomyList::DATA_TYPE_LABELS[$taxonomyList->data_type] ?? ucfirst($taxonomyList->data_typ) }}
                        </dd>
                    </div>
                </div>

                <div class="card-body">
                    <div x-data="{
                        items: @js($taxonomyList->items ?? []),
                        syncItems(value) {
                            this.items = Array.isArray(value) ? value : [];
                            this.$refs.itemsInput.value = JSON.stringify(this.items);
                        }
                    }" x-init="syncItems(items)" x-on:items-changed="syncItems($event.detail)">
                        <input type="hidden" name="items" x-ref="itemsInput">
                        @livewire('backend.taxonomy-list-item-manager', [
                            'type' => $taxonomyList->data_type,
                            'title' => 'List Items',
                            'items' => $taxonomyList->items,
                            'files' => $files,
                            'pages' => $pages,
                        ])
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            {!! Form::submit(__('Update'), ['class' => 'btn btn-primary btn-w-150 float-end']) !!}
            <a href="{{ route('dashboard.taxonomy-lists.index') }}"
                class="btn btn-light btn-outline-secondary btn-w-150 float-end mr-2">Back</a>
        </x-slot>
    </x-backend.card>

    {!! Form::close() !!}
@endsection
