@props(['href' => '#', 'permission' => false, 'icon' => 'fas fa-search'])

<x-utils.link :href="$href" class="btn btn-info btn-sm" icon="{{ $icon }}" permission="{{ $permission }}" />
