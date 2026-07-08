@extends('layouts.admin')

@section('title', 'Œuvres')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form method="GET" action="{{ route('admin.oeuvres.index') }}" class="d-flex flex-wrap gap-2">
            <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Rechercher une œuvre…" aria-label="Recherche">
            </div>
            <select name="tag" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="">Tous les tags</option>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(request('tag') == $tag->id)>{{ $tag->nom }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
        </form>

        <a href="{{ route('admin.oeuvres.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvelle œuvre
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 64px;">Photo</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Tags</th>
                        <th>Localisation</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($images as $image)
                        <tr>
                            <td>
                                <img src="{{ $image->photoUrl() }}" alt="{{ $image->nom }}" class="img-thumb-table" loading="lazy">
                            </td>
                            <td>
                                <a href="{{ route('admin.oeuvres.show', $image) }}" class="fw-semibold text-decoration-none">
                                    {{ $image->nom }}
                                </a>
                            </td>
                            <td class="text-nowrap">
                                {{ $image->prix !== null ? number_format($image->prix, 2, ',', ' ').' €' : '—' }}
                            </td>
                            <td>
                                @forelse ($image->tags as $tag)
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $tag->nom }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </td>
                            <td>
                                @if ($image->location)
                                    <span class="text-nowrap small">
                                        <i class="bi bi-geo-alt text-success me-1"></i>{{ $image->location->latitude }}, {{ $image->location->longitude }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.oeuvres.edit', $image) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger js-confirm-delete"
                                        data-action="{{ route('admin.oeuvres.destroy', $image) }}"
                                        data-label="l'œuvre « {{ $image->nom }} »" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune œuvre trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($images->hasPages())
            <div class="card-footer bg-white">
                {{ $images->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
