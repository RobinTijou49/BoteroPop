@csrf

<div class="card" style="max-width: 560px;">
    <div class="card-header bg-white fw-semibold">{{ $tag->exists ? 'Modifier le tag' : 'Nouveau tag' }}</div>
    <div class="card-body">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $tag->nom) }}"
                   class="form-control @error('nom') is-invalid @enderror" required autofocus>
            @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>{{ $tag->exists ? 'Enregistrer' : 'Créer le tag' }}
            </button>
            <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-secondary">Annuler</a>
        </div>
    </div>
</div>
