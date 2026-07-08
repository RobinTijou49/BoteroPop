@extends('layouts.admin')

@section('title', 'Modifier l\'œuvre')

@section('content')
    <form method="POST" action="{{ route('admin.oeuvres.update', $image) }}" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.images._form')
    </form>
@endsection
