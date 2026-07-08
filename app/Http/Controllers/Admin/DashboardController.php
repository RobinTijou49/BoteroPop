<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Image;
use App\Models\Inscription;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'imagesCount' => Image::count(),
            'eventsCount' => Event::count(),
            'inscriptionsCount' => Inscription::count(),
            'upcomingEvents' => Event::upcoming()
                ->withCount('inscriptions')
                ->take(5)
                ->get(),
        ]);
    }
}
