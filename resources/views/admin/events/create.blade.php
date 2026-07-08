@extends('layouts.admin')

@section('title', 'Nouvel évènement')

@section('content')
    <form method="POST" action="{{ route('admin.evenements.store') }}">
        @include('admin.events._form')
    </form>
@endsection
