@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <form method="POST" action="{{ route('admin.parametres.profil') }}">
                @csrf
                @method('PATCH')

                <div class="card">
                    <div class="card-header bg-white fw-semibold">Mon profil</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Enregistrer le profil
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header bg-white fw-semibold">Changer mon mot de passe</div>
                    <div class="card-body">
                        @if (session('status') === 'password-updated')
                            <div class="alert alert-success">Mot de passe mis à jour avec succès.</div>
                        @endif

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Mot de passe actuel <span class="text-danger">*</span></label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   required autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   required autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmation <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key me-1"></i>Mettre à jour le mot de passe
                        </button>
                    </div>
                </div>
            </form>

            <div class="card mt-4">
                <div class="card-header bg-white fw-semibold">Application</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Nom de l'application</dt>
                        <dd class="col-sm-7">{{ config('app.name') }}</dd>

                        <dt class="col-sm-5">Environnement</dt>
                        <dd class="col-sm-7">{{ config('app.env') }}</dd>

                        <dt class="col-sm-5">Version Laravel</dt>
                        <dd class="col-sm-7 mb-0">{{ app()->version() }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
