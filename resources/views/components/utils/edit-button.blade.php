@props(['href' => '#', 'permission' => false])

<x-utils.link :href="$href" class="btn btn-primary btn-sm" icon="fas fa-pencil-alt" permission="{{ $permission }}" />
