@extends('layouts.admin')

@section('title', 'Modifier l\'utilisateur')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-lg-6">
            <form method="POST" action="{{ route('admin.utilisateurs.update', $utilisateur) }}">
                @csrf
                @method('PUT')

                <div class="card">
                    <div class="card-header bg-white fw-semibold">Informations du compte</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $utilisateur->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $utilisateur->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                   @checked(old('is_active', $utilisateur->is_active))
                                   @disabled($utilisateur->is(auth()->user()))>
                            <label class="form-check-label" for="is_active">Compte actif</label>
                            @if ($utilisateur->is(auth()->user()))
                                <div class="form-text">Vous ne pouvez pas désactiver votre propre compte.</div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Enregistrer
                            </button>
                            <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-6">
            <form method="POST" action="{{ route('admin.utilisateurs.reset-password', $utilisateur) }}">
                @csrf
                @method('PATCH')

                <div class="card">
                    <div class="card-header bg-white fw-semibold">Réinitialisation du mot de passe</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmation <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-key me-1"></i>Réinitialiser le mot de passe
                        </button>
                    </div>
                </div>
            </form>

            @unless ($utilisateur->is(auth()->user()))
                <div class="card mt-4 border border-danger-subtle">
                    <div class="card-header bg-white fw-semibold text-danger">Zone dangereuse</div>
                    <div class="card-body d-flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.utilisateurs.toggle-active', $utilisateur) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-warning">
                                <i class="bi bi-{{ $utilisateur->is_active ? 'pause' : 'play' }} me-1"></i>
                                {{ $utilisateur->is_active ? 'Désactiver le compte' : 'Réactiver le compte' }}
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger js-confirm-delete"
                                data-action="{{ route('admin.utilisateurs.destroy', $utilisateur) }}"
                                data-label="l'utilisateur {{ $utilisateur->name }}">
                            <i class="bi bi-trash me-1"></i>Supprimer le compte
                        </button>
                    </div>
                </div>
            @endunless
        </div>
    </div>
@endsection
