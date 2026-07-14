@extends('layouts.admin')
@section('title', ucfirst(str_replace('_', ' ', $role)) . ' Dashboard')
@section('page_title', ucfirst(str_replace('_', ' ', $role)) . ' Dashboard')
@section('page_actions')
@endsection
@section('content')
    @include('dashboard.role-content')
@endsection
