@extends('backend.layouts.app')

@section('title', __('Edit Taxonomy Page'))

@section('content')
    @livewire('backend.taxonomy-pages-form', ['taxonomyPage' => $taxonomyPage])
@endsection
