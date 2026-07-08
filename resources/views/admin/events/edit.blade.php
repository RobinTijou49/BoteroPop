@extends('layouts.admin')

@section('title', 'Modifier l\'évènement')

@section('content')
    <form method="POST" action="{{ route('admin.evenements.update', $event) }}">
        @method('PUT')
        @include('admin.events._form')
    </form>
@endsection
