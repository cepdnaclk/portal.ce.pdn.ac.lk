@extends('backend.layouts.app')

@section('title', __('Courses'))

@section('content')
    @livewire('backend.edit-courses',[$course])
@endsection
