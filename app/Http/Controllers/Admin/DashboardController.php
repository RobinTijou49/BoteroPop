<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Image;
use App\Models\Inscription;
use App\Models\Reservation;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'imagesCount' => Image::count(),
            'reservationsCount' => Reservation::count(),
            'eventsCount' => Event::count(),
            'inscriptionsCount' => Inscription::count(),
            'latestReservations' => Reservation::with(['image' => fn ($q) => $q->select(Image::COLUMNS_WITHOUT_BLOB)])
                ->latest('reserved_at')
                ->take(5)
                ->get(),
            'upcomingEvents' => Event::upcoming()
                ->withCount('inscriptions')
                ->take(5)
                ->get(),
        ]);
    }
}
