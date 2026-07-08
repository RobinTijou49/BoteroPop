@extends('layouts.admin')

@section('title', 'Évènements')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <form method="GET" action="{{ route('admin.evenements.index') }}" class="d-flex gap-2">
            <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Rechercher un évènement…" aria-label="Recherche">
            </div>
            <button type="submit" class="btn btn-outline-secondary">Rechercher</button>
        </form>

        <a href="{{ route('admin.evenements.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Nouvel évènement
        </a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Lieu</th>
                        <th>Date</th>
                        <th class="text-end">Inscrits</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>
                                <a href="{{ route('admin.evenements.show', $event) }}" class="fw-semibold text-decoration-none">
                                    {{ $event->nom }}
                                </a>
                                @if ($event->date->isPast() && ! $event->date->isToday())
                                    <span class="badge text-bg-light text-muted">Passé</span>
                                @endif
                            </td>
                            <td>{{ $event->lieu }}</td>
                            <td class="text-nowrap">{{ $event->date->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <span class="badge bg-info-subtle text-info-emphasis">{{ $event->inscriptions_count }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('admin.evenements.show', $event) }}" class="btn btn-sm btn-outline-secondary" title="Consulter">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.evenements.edit', $event) }}" class="btn btn-sm btn-outline-primary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger js-confirm-delete"
                                        data-action="{{ route('admin.evenements.destroy', $event) }}"
                                        data-label="l'évènement « {{ $event->nom }} »" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Aucun évènement trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($events->hasPages())
            <div class="card-footer bg-white">
                {{ $events->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
