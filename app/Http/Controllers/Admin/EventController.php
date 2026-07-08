<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::withCount('inscriptions')
            ->when($request->string('q')->toString(), function ($query, $term) {
                $query->where('nom', 'like', "%{$term}%");
            })
            ->orderByDesc('date')
            ->paginate(10)
            ->withQueryString();

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create', ['event' => new Event]);
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());

        return redirect()
            ->route('admin.evenements.show', $event)
            ->with('success', 'Évènement créé avec succès.');
    }

    public function show(Event $event)
    {
        $event->loadCount('inscriptions');
        $inscriptions = $event->inscriptions()
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.events.show', compact('event', 'inscriptions'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event)
    {
        $event->update($request->validated());

        return redirect()
            ->route('admin.evenements.show', $event)
            ->with('success', 'Évènement mis à jour avec succès.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.evenements.index')
            ->with('success', 'Évènement supprimé avec succès.');
    }
}
