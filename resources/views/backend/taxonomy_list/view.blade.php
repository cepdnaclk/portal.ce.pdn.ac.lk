@extends('backend.layouts.app')

@section('title', __('View Taxonomy List'))

@section('content')
    <x-backend.card>
        <x-slot name="header">
            Taxonomy List: {{ $taxonomyList->name }}
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
                            {{ $taxonomyList::DATA_TYPE_LABELS[$taxonomyList->data_type] ?? ucfirst($taxonomyList->data_type) }}
                        </dd>
                    </div>
                </div>
            </div>

            @if (empty($taxonomyList->items))
                <p class="text-muted">No items recorded yet.</p>
            @else
                <ol class="list-group list-group-numbered">
                    @foreach ($taxonomyList->items as $item)
                        <li class="list-group-item">
                            @if ($taxonomyList->data_type === 'file')
                                @if ($file = $fileMap->get($item))
                                    <a href="{{ route('download.taxonomy-file', ['file_name' => $file->file_name, 'extension' => $file->getFileExtension()]) }}"
                                        target="_blank">
                                        {{ $file->file_name }}
                                    </a>
                                @else
                                    Missing file (#{{ $item }})
                                @endif
                            @elseif ($taxonomyList->data_type === 'page')
                                @if ($page = $pageMap->get($item))
                                    <a href="{{ route('dashboard.taxonomy-pages.view', $page) }}" target="_blank">
                                        {{ $page->slug }}
                                    </a>
                                @else
                                    Missing page (#{{ $item }})
                                @endif
                            @elseif ($taxonomyList->data_type === 'url')
                                <a href="{{ $item }}" target="_blank">{{ $item }}</a>
                            @else
                                {{ $item }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-slot>

        <x-slot name="footer">
            <a href="{{ route('dashboard.taxonomy-lists.index') }}" class="btn btn-light float-end btn-w-150">Back</a>
        </x-slot>

    </x-backend.card>
@endsection
