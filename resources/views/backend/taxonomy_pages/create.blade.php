@extends('backend.layouts.app')

@section('title', __('Create Taxonomy Page'))

@section('content')
    @livewire('backend.taxonomy-pages-form')
@endsection
