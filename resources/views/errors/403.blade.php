@extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __("Vous n'avez pas la permission d'accéder à cette page"))
