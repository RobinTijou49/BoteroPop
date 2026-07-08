<x-guest-layout>
    <h1 class="h5 mb-3 text-center">Mot de passe oublié</h1>

    <p class="text-muted small">
        Indiquez votre adresse e-mail et nous vous enverrons un lien de réinitialisation
        pour choisir un nouveau mot de passe.
    </p>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror"
                   required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mb-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-1"></i>Envoyer le lien de réinitialisation
            </button>
        </div>

        <div class="text-center">
            <a href="{{ route('login') }}" class="small text-decoration-none">Retour à la connexion</a>
        </div>
    </form>
</x-guest-layout>
