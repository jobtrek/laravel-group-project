@extends('errors.minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Surchage de requête, veuillez patienter avant de réessayer.'))
