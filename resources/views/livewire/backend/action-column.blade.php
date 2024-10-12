<div>
    <div class="d-flex px-0 mt-0 mb-0">
        <div class="btn-group" role="group" aria-label="">
            @isset($editLink)
                <a href="{{ $editLink }}" class="btn btn-sm btn-info"><i class="fa fa-pencil" title="Edit"></i>
                </a>
            @endisset

            @isset($deleteLink)
                <a href="{{ $deleteLink }}" class="btn btn-sm btn-danger"><i class="fa fa-trash" title="Delete"></i>
                </a>
            @endisset
        </div>
    </div>
</div>
