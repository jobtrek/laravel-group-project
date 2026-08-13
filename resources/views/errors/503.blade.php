@extends('errors.minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __("L'application est en cours de maintenance, réessayez plus tard."))
