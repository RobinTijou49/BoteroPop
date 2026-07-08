<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Inscription;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function index(Request $request)
    {
        // La table WordPress ne stocke pas de date d'inscription : le tri
        // chronologique s'appuie sur l'ordre d'ajout (id auto-incrémenté).
        $sort = $request->input('sort') === 'asc' ? 'asc' : 'desc';

        $inscriptions = Inscription::with('event')
            ->when($request->integer('event'), function ($query, $eventId) {
                $query->where('id_event', $eventId);
            })
            ->when($request->string('q')->toString(), function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('nom', 'like', "%{$term}%")
                        ->orWhere('prenom', 'like', "%{$term}%");
                });
            })
            ->orderBy('id', $sort)
            ->paginate(15)
            ->withQueryString();

        return view('admin.inscriptions.index', [
            'inscriptions' => $inscriptions,
            'events' => Event::orderByDesc('date')->get(),
            'sort' => $sort,
        ]);
    }

    public function destroy(Inscription $inscription)
    {
        $inscription->delete();

        return redirect()
            ->route('admin.inscriptions.index')
            ->with('success', 'Inscription supprimée avec succès.');
    }
}
