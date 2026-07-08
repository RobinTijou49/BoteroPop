@extends('layouts.admin')

@section('title', 'Nouveau tag')

@section('content')
    <form method="POST" action="{{ route('admin.tags.store') }}">
        @include('admin.tags._form')
    </form>
@endsection
