@extends('layouts.admin')

@section('title', 'Nouvelle œuvre')

@section('content')
    <form method="POST" action="{{ route('admin.oeuvres.store') }}" enctype="multipart/form-data">
        @include('admin.images._form')
    </form>
@endsection
