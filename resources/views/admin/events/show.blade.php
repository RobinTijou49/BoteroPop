@extends('layouts.admin')

@section('title', $event->nom)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="{{ route('admin.evenements.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour aux évènements
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.evenements.edit', $event) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            <button type="button" class="btn btn-outline-danger js-confirm-delete"
                    data-action="{{ route('admin.evenements.destroy', $event) }}"
                    data-label="l'évènement « {{ $event->nom }} »">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header bg-white fw-semibold">Détails</div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Description</dt>
                        <dd>{{ $event->description }}</dd>

                        <dt>Lieu</dt>
                        <dd>{{ $event->lieu }}</dd>

                        <dt>Date</dt>
                        <dd class="mb-0">
                            {{ $event->date->format('d/m/Y') }}
                            @if ($event->date->isPast() && ! $event->date->isToday())
                                <span class="badge text-bg-light text-muted">Passé</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-white fw-semibold">
                    Inscrits <span class="badge text-bg-info">{{ $event->inscriptions_count }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Prénom</th>
                                <th>Nom</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($inscriptions as $inscription)
                                <tr>
                                    <td>{{ $inscription->prenom }}</td>
                                    <td>{{ $inscription->nom }}</td>
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
                                    <td colspan="3" class="text-center text-muted py-4">Aucun inscrit pour cet évènement.</td>
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
        </div>
    </div>
@endsection
