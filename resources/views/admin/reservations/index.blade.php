@extends('layouts.admin')

@section('title', 'Réservations')

@section('content')
    <form method="GET" action="{{ route('admin.reservations.index') }}" class="d-flex flex-wrap gap-2 mb-3">
        <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Rechercher par client ou e-mail…" aria-label="Recherche">
        </div>

        <select name="statut" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            @foreach (\App\Models\Reservation::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(request('statut') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-outline-secondary">Filtrer</button>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Client</th>
                        <th>E-mail</th>
                        <th>Œuvre</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reservations as $reservation)
                        <tr>
                            <td class="fw-semibold">{{ $reservation->customer_name }}</td>
                            <td><a href="mailto:{{ $reservation->email }}" class="text-decoration-none">{{ $reservation->email }}</a></td>
                            <td>
                                @if ($reservation->image)
                                    <a href="{{ route('admin.oeuvres.show', $reservation->image) }}" class="text-decoration-none">
                                        {{ $reservation->image->nom }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.reservations.update-status', $reservation) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()"
                                            aria-label="Statut de la réservation">
                                        @foreach (\App\Models\Reservation::STATUSES as $value => $label)
                                            <option value="{{ $value }}" @selected($reservation->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="text-nowrap">{{ $reservation->reserved_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger js-confirm-delete"
                                        data-action="{{ route('admin.reservations.destroy', $reservation) }}"
                                        data-label="la réservation de {{ $reservation->customer_name }}" title="Supprimer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune réservation trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reservations->hasPages())
            <div class="card-footer bg-white">
                {{ $reservations->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
