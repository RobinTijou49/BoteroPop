<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * @param  array<string, mixed>  $data  données validées
     */
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_ADMIN,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data  données validées
     */
    public function update(User $user, array $data): User
    {
        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (array_key_exists('is_active', $data)) {
            $this->guardLastActiveAdmin($user, (bool) $data['is_active']);
            $user->is_active = (bool) $data['is_active'];
        }

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return $user;
    }

    public function resetPassword(User $user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
    }

    public function toggleActive(User $user, User $actor): User
    {
        if ($user->is($actor)) {
            throw ValidationException::withMessages([
                'user' => 'Vous ne pouvez pas désactiver votre propre compte.',
            ]);
        }

        $this->guardLastActiveAdmin($user, ! $user->is_active);

        $user->update(['is_active' => ! $user->is_active]);

        return $user;
    }

    public function delete(User $user, User $actor): void
    {
        if ($user->is($actor)) {
            throw ValidationException::withMessages([
                'user' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ]);
        }

        $this->guardLastActiveAdmin($user, false);

        $user->delete();
    }

    /**
     * Empêche de désactiver ou supprimer le dernier administrateur actif.
     */
    private function guardLastActiveAdmin(User $user, bool $willBeActive): void
    {
        if ($willBeActive || ! $user->is_active) {
            return;
        }

        $otherActiveAdmins = User::where('id', '!=', $user->id)
            ->where('is_active', true)
            ->count();

        if ($otherActiveAdmins === 0) {
            throw ValidationException::withMessages([
                'user' => 'Impossible : il doit rester au moins un administrateur actif.',
            ]);
        }
    }
}
