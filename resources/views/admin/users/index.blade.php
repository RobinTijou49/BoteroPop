@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form method="GET" action="{{ route('admin.utilisateurs.index') }}" class="d-flex gap-2">
            <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Rechercher un utilisateur…" aria-label="Recherche">
            </div>
            <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
        </form>

        <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i>Nouvel utilisateur
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($utilisateurs as $utilisateur)
                        <tr>
                            <td class="fw-semibold">
                                {{ $utilisateur->name }}
                                @if ($utilisateur->is(auth()->user()))
                                    <span class="badge text-bg-light text-muted">Vous</span>
                                @endif
                            </td>
                            <td>{{ $utilisateur->email }}</td>
                            <td><span class="badge bg-primary-subtle text-primary-emphasis">{{ ucfirst($utilisateur->role) }}</span></td>
                            <td>
                                @if ($utilisateur->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis">Actif</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Désactivé</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $utilisateur->created_at->format('d/m/Y') }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.utilisateurs.edit', $utilisateur) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @unless ($utilisateur->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.utilisateurs.toggle-active', $utilisateur) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning"
                                                title="{{ $utilisateur->is_active ? 'Désactiver' : 'Réactiver' }}">
                                            <i class="bi bi-{{ $utilisateur->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger js-confirm-delete"
                                            data-action="{{ route('admin.utilisateurs.destroy', $utilisateur) }}"
                                            data-label="l'utilisateur {{ $utilisateur->name }}" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($utilisateurs->hasPages())
            <div class="card-footer bg-white">
                {{ $utilisateurs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
