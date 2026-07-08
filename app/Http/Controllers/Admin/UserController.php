<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetUserPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request)
    {
        $utilisateurs = User::query()
            ->when($request->string('q')->toString(), function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('utilisateurs'));
    }

    public function create()
    {
        return view('admin.users.create', ['utilisateur' => new User]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $utilisateur)
    {
        return view('admin.users.edit', compact('utilisateur'));
    }

    public function update(UpdateUserRequest $request, User $utilisateur)
    {
        $this->userService->update($utilisateur, $request->validated());

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $utilisateur)
    {
        $this->userService->resetPassword($utilisateur, $request->validated('password'));

        return redirect()
            ->route('admin.utilisateurs.edit', $utilisateur)
            ->with('success', 'Mot de passe réinitialisé avec succès.');
    }

    public function toggleActive(Request $request, User $utilisateur)
    {
        $this->userService->toggleActive($utilisateur, $request->user());

        $message = $utilisateur->is_active
            ? 'Compte réactivé avec succès.'
            : 'Compte désactivé avec succès.';

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', $message);
    }

    public function destroy(Request $request, User $utilisateur)
    {
        $this->userService->delete($utilisateur, $request->user());

        return redirect()
            ->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
