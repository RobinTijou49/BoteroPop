<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BoteroPop') }} — Connexion</title>

        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    </head>
    <body class="d-flex align-items-center py-4" style="min-height: 100vh; background-color: #000000;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                    <div class="text-center mb-4">
                        <span class="d-inline-block bg-white rounded px-3 py-2">
                            <img src="{{ asset('images/botero_logo.png') }}" alt="BoteroPop" style="max-width: 220px; width: 100%; height: auto;">
                        </span>
                        <p class="text-white-50 mb-0 mt-2">Back Office d'administration</p>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
