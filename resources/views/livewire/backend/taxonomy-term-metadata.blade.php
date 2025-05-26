<div class="col-12 py-2">
    <div class="col ps-0">
        <label>{{ $property['name'] }}
            ({{ \App\Domains\Taxonomy\Models\Taxonomy::$propertyType[$property['data_type']] }})
        </label>
    </div>
    <div class="col-md-12 px-0">
        @php
            $value = null;
            if (!empty($property['code']) && $term) {
                $value = $term->getMetadata($property['code']);
            }
        @endphp

        @switch($property['data_type'])
            @case('string')
                {!! Form::text("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
            @break

            @case('email')
                {!! Form::email("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
            @break

            @case('integer')
                {!! Form::number("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                    'step' => '1',
                ]) !!}
            @break

            @case('float')
                {!! Form::number("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                    'step' => 'any',
                ]) !!}
            @break

            @case('boolean')
                <div class="form-check">
                    {!! Form::checkbox("metadata[{$property['code']}]", 1, old("metadata.{$property['code']}", $value == 1), [
                        'class' => 'form-check-input',
                        'id' => $property['code'],
                    ]) !!}
                </div>
            @break

            @case('date')
                {!! Form::date("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
            @break

            @case('datetime')
                {!! Form::datetimeLocal("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
            @break

            @case('url')
                {!! Form::url("metadata[{$property['code']}]", old("metadata.{$property['code']}", $value), [
                    'class' => 'form-control',
                    'id' => $property['code'],
                ]) !!}
            @break

            @case('image')
                {!! Form::file("metadata[{$property['code']}]", ['class' => 'form-control', 'id' => $property['code']]) !!}
                @if ($value)
                    <small>Current: {{ $value }}</small>
                @endif
            @break

            @case('file')
                @if (empty($taxonomy_files))
                    <p><i>No files available for selection. </i></p>
                @else
                    @php
                        $fileOptions = ['' => 'Select an option'];
                        foreach ($taxonomy_files as $file) {
                            $fileOptions[$file['id']] = $file['file_name'];
                        }
                    @endphp
                    {!! Form::select(
                        "metadata[{$property['code']}_dropdown]",
                        $fileOptions,
                        old("metadata.{$property['code']}_dropdown"),
                        [
                            'class' => 'form-control mt-2',
                            'id' => $property['code'] . '_dropdown',
                        ],
                    ) !!}
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
