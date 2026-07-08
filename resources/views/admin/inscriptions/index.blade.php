@extends('layouts.admin')

@section('title', 'Inscriptions')

@section('content')
    <form method="GET" action="{{ route('admin.inscriptions.index') }}" class="d-flex flex-wrap gap-2 mb-3">
        <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Rechercher par nom ou prénom…" aria-label="Recherche">
        </div>

        <select name="event" class="form-select" style="max-width: 280px;" onchange="this.form.submit()">
            <option value="">Tous les évènements</option>
            @foreach ($events as $event)
                <option value="{{ $event->id }}" @selected(request('event') == $event->id)>
                    {{ $event->nom }} ({{ $event->date->format('d/m/Y') }})
                </option>
            @endforeach
        </select>

        <input type="hidden" name="sort" value="{{ $sort }}">
        <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Évènement</th>
                        <th>Date de l'évènement</th>
                        <th>
                            <a href="{{ route('admin.inscriptions.index', array_merge(request()->except('page'), ['sort' => $sort === 'desc' ? 'asc' : 'desc'])) }}"
                               class="text-decoration-none text-body">
                                Ordre d'inscription
                                <i class="bi bi-sort-{{ $sort === 'desc' ? 'down' : 'up' }}"></i>
                            </a>
                        </th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inscriptions as $inscription)
                        <tr>
                            <td>{{ $inscription->prenom }}</td>
                            <td class="fw-semibold">{{ $inscription->nom }}</td>
                            <td>
                                @if ($inscription->event)
                                    <a href="{{ route('admin.evenements.show', $inscription->event) }}" class="text-decoration-none">
                                        {{ $inscription->event->nom }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $inscription->event?->date->format('d/m/Y') ?? '—' }}</td>
                            <td>n° {{ $inscription->id }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger js-confirm-delete"
                                        data-action="{{ route('admin.inscriptions.destroy', $inscription) }}"
                                        data-label="l'inscription de {{ $inscription->fullName() }}" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune inscription trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($inscriptions->hasPages())
            <div class="card-footer bg-white">
                {{ $inscriptions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
