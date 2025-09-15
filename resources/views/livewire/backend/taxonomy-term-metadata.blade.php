@php
    $value = null;
    if (!empty($property['code']) && $term) {
        $value = $term->getFormattedMetadata($property['code']);
    }
@endphp

<div class="col-12 pb-2">
    <div class="col ps-0">
        <label>
            {{ $property['name'] }}
            ({{ \App\Domains\Taxonomy\Models\Taxonomy::$propertyType[$property['data_type']] }})

            @if ($property['data_type'] == 'file' && $logged_in_user->hasPermissionTo('user.access.taxonomy.file.editor'))
                {{-- Taxonomy File --}}
                <span class="ms-2">
                    <a class="ms-2 text-decoration-none" target="_blank"
                        href="{{ route('dashboard.taxonomy-files.create') }}">
                        <i class="fa fa-plus"></i>
                    </a>

                    @if (!empty($value) && isset($taxonomy_files[$value]))
                        <a class="ms-2 text-decoration-none" target="_blank"
                            href="{{ route('dashboard.taxonomy-files.edit', $value) }}">
                            <i class="fa fa-pencil"></i>
                        </a>
                    @endif
                </span>
            @elseif ($property['data_type'] == 'page' && $logged_in_user->hasPermissionTo('user.access.taxonomy.page.editor'))
                {{-- Taxonomy Page --}}
                <span class="ms-2">
                    <a class="ms-2 text-decoration-none" target="_blank"
                        href="{{ route('dashboard.taxonomy-pages.create') }}">
                        <i class="fa fa-plus"></i>
                    </a>

                    @if (!empty($value) && isset($taxonomy_pages[$value]))
                        <a class="ms-2 text-decoration-none" target="_blank"
                            href="{{ route('dashboard.taxonomy-pages.edit', $value) }}">
                            <i class="fa fa-pencil"></i>
                        </a>
                    @endif
                </span>
            @endif
        </label>
    </div>
    <div class="col-md-12 px-0 pb-2">
        @switch($property['data_type'])
            @case('string')
                {!! Form::text("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
            @break

            @case('email')
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa fa-envelope"></i>
                    </span>
                    {!! Form::email("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                        'class' => 'form-control',
                        'id' => $property['code'],
                    ]) !!}
                </div>
            @break

            @case('integer')
                <div class="input-group">
                    <span class="input-group-text">#</span>
                    {!! Form::number("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                        'class' => 'form-control',
                        'id' => $property['code'],
                        'step' => '1',
                    ]) !!}
                </div>
            @break

            @case('float')
                <div class="input-group">
                    <span class="input-group-text">#.#</span>
                    {!! Form::number("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                        'class' => 'form-control',
                        'id' => $property['code'],
                        'step' => 'any',
                    ]) !!}
                </div>
            @break

            @case('boolean')
                <div class="form-check">
                    {!! Form::checkbox("metadata[{$property['code']}]", 1, old("metadata.{$property['code']}", $value == 1), [
                        'class' => 'form-check-input ms-2',
                        'id' => $property['code'],
                        'style' => 'width: 1.15em; height: 1.15em;',
                    ]) !!}
                </div>
            @break

            @case('date')
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </span>
                    {!! Form::date("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                        'class' => 'form-control',
                        'id' => $property['code'],
                    ]) !!}
                </div>
            @break

            @case('datetime')
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa fa-clock"></i>
                    </span>
                    {!! Form::datetimeLocal("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                        'class' => 'form-control',
                        'id' => $property['code'],
                    ]) !!}
                </div>
            @break

            @case('url')
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa fa-link"></i>
                    </span>
                    {!! Form::url("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                        'class' => 'form-control',
                        'id' => $property['code'],
                        'placeholder' => 'Enter a valid URL',
                    ]) !!}
                </div>
            @break

            @case('file')
                @if (empty($taxonomy_files))
                    <p><i>No files available for selection. </i></p>
                @else
                    <livewire:backend.searchable-dropdown :name="'metadata[' . $property['code'] . ']'" :options="collect($taxonomy_files)->sort()->toArray()" :selected="old('metadata.' . $property['code'], $value)"
                        :placeholder="$taxonomy_files[''] ?? 'Select a file'" :icon="'fa fa-file'" :inputId="$property['code']" />
                @endif
            @break

            @case('page')
                @if (empty($taxonomy_pages))
                    <p><i>No page available for selection.</i></p>
                @else
                    <livewire:backend.searchable-dropdown :name="'metadata[' . $property['code'] . ']'" :options="collect($taxonomy_pages)->sort()->toArray()" :selected="old('metadata.' . $property['code'], $value)"
                        :placeholder="$taxonomy_pages[''] ?? 'Select a page'" :icon="'fa fa-globe'" :inputId="$property['code']" />
                @endif
            @break

            @default
                {!! Form::text("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
        @endswitch
    </div>

</div>
