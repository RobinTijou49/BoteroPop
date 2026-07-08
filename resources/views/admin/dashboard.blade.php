@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- Cartes statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle text-primary-emphasis"><i class="bi bi-image"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $imagesCount }}</div>
                        <div class="text-muted small">Œuvres</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $eventsCount }}</div>
                        <div class="text-muted small">Évènements</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-4">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle text-info-emphasis"><i class="bi bi-people"></i></div>
                    <div>
                        <div class="fs-3 fw-bold">{{ $inscriptionsCount }}</div>
                        <div class="text-muted small">Inscriptions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Prochains évènements --}}
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-calendar-event me-2"></i>Prochains évènements</span>
            <a href="{{ route('admin.evenements.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Évènement</th>
                        <th>Date</th>
                        <th class="text-end">Inscrits</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($upcomingEvents as $event)
                        <tr>
                            <td>
                                <a href="{{ route('admin.evenements.show', $event) }}" class="text-decoration-none">
                                    {{ $event->nom }}
                                </a>
                            </td>
                            <td class="text-nowrap">{{ $event->date->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <span class="badge bg-info-subtle text-info-emphasis">{{ $event->inscriptions_count }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Aucun évènement à venir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
