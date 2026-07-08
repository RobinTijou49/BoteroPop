@extends('layouts.admin')

@section('title', 'Modifier le tag')

@section('content')
    <form method="POST" action="{{ route('admin.tags.update', $tag) }}">
        @method('PUT')
        @include('admin.tags._form')
    </form>
@endsection
