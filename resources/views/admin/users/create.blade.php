@extends('layouts.admin')

@section('title', 'Nouvel utilisateur')

@section('content')
    <form method="POST" action="{{ route('admin.utilisateurs.store') }}">
        @csrf

        <div class="card" style="max-width: 640px;">
            <div class="card-header bg-white fw-semibold">Informations du compte</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse e-mail <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmation <span class="text-danger">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control" required autocomplete="new-password">
                    </div>
                </div>

                <div class="mb-3 form-check form-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                           @checked(old('is_active', true))>
                    <label class="form-check-label" for="is_active">Compte actif</label>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-person-plus me-1"></i>Créer l'utilisateur
                    </button>
                    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </div>
        </div>
    </form>
@endsection
