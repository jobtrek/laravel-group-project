@extends('errors.minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __("Vous devez être connecté pour accéder à cette page"))
