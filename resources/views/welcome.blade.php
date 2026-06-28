@extends('layouts.landing')

@section('title', config('app.name') . ' - Enterprise Resource Planning')

@section('content')
    @include('landing.partials.header')
    @include('landing.partials.hero')
    @include('landing.partials.features')
    @include('landing.partials.modules')
    @include('landing.partials.about')
    @include('landing.partials.workflow')
    @include('landing.partials.cta')
    @include('landing.partials.footer')
@endsection