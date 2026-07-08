<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('admin.settings.edit', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $request->user()->update($request->validated());

        return redirect()
            ->route('admin.parametres.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}
