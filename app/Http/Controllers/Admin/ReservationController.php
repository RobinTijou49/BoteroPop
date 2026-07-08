<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::with(['image' => fn ($q) => $q->select(\App\Models\Image::COLUMNS_WITHOUT_BLOB)])
            ->when($request->string('q')->toString(), function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('customer_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->string('statut')->toString(), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest('reserved_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reservations.index', compact('reservations'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Reservation::STATUSES))],
        ]);

        $reservation->update($validated);

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Statut de la réservation mis à jour.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()
            ->route('admin.reservations.index')
            ->with('success', 'Réservation supprimée avec succès.');
    }
}
