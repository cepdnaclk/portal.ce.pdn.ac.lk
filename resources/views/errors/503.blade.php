@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Site Under Maintenance. Please wait for a few minutes and retry'))
