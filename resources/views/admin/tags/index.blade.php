@extends('layouts.admin')

@section('title', 'Tags')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form method="GET" action="{{ route('admin.tags.index') }}" class="d-flex gap-2">
            <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Rechercher un tag…" aria-label="Recherche">
            </div>
            <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
        </form>

        <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouveau tag
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th class="text-end">Œuvres associées</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td class="fw-semibold">{{ $tag->nom }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.oeuvres.index', ['tag' => $tag->id]) }}" class="badge bg-secondary-subtle text-secondary-emphasis text-decoration-none">
                                    {{ $tag->images_count }}
                                </a>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger js-confirm-delete"
                                        data-action="{{ route('admin.tags.destroy', $tag) }}"
                                        data-label="le tag « {{ $tag->nom }} »" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Aucun tag trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tags->hasPages())
            <div class="card-footer bg-white">
                {{ $tags->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
