<x-livewire-tables::table.cell>
    {{ $row->title }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-1" style="width: 75px;">
        {{ $row->created_at }}
    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-1" style="width: 75px;">
        {{ $row->updated_at }}
    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="custom-width-2" style="width: 175px;">
        @php
        $words = explode(' ', $row->description);
        $limitedDescription = implode(' ', array_slice($words, 0, 50));
        $remainingWords = count($words) - 50;
    @endphp
    {!! $remainingWords > 0 ? $limitedDescription . '&nbsp;<a href="#" class="show-more" data-id="' . $row->id . '">Show more >>></a><span id="full-description-' . $row->id . '" style="display: none;">' . implode(' ', array_slice($words, 10)) . '</span><a href="#" class="show-less" data-id="' . $row->id . '" style="display: none;"> Show less <<<</a>' : $row->description !!}

    </div>
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <img class="mt-1" src="{{ $row->image ? asset('storage/' . $row->image) : asset('EventItems/no-image.png') }}" alt="Image preview" style="max-width: 200px; max-height: 200px;" />
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->enabled ? 'Enabled' : 'Disabled' }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->link_url }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    {{ $row->link_caption }}
</x-livewire-tables::table.cell>

<x-livewire-tables::table.cell>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            <a href="{{ route('dashboard.event.edit', $row) }}" class="btn btn-info btn-xs"><i class="fa fa-pencil"
                    title="Edit"></i>
            </a>
            <a href="{{ route('dashboard.event.delete', $row) }}" class="btn btn-danger btn-xs"><i
                    class="fa fa-trash" title="Delete"></i>
            </a>
        </div>
    </div>
</x-livewire-tables::table.cell>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.show-more').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                var id = event.target.dataset.id;
                var descriptionElement = document.getElementById('full-description-' + id);
                var showMoreLink = event.target;
                var showLessLink = document.querySelector('.show-less[data-id="' + id + '"]');
                
                descriptionElement.style.display = 'inline';
                showMoreLink.style.display = 'none';
                showLessLink.style.display = 'inline';
            });
        });

        document.querySelectorAll('.show-less').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                var id = event.target.dataset.id;
                var descriptionElement = document.getElementById('full-description-' + id);
                var showMoreLink = document.querySelector('.show-more[data-id="' + id + '"]');
                var showLessLink = event.target;
                
                descriptionElement.style.display = 'none';
                showMoreLink.style.display = 'inline';
                showLessLink.style.display = 'none';
            });
        });
    });
</script>
