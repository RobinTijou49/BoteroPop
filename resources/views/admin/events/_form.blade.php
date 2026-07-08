@csrf

<div class="card">
    <div class="card-header bg-white fw-semibold">Informations de l'évènement</div>
    <div class="card-body">
        <div class="mb-3">
            <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $event->nom) }}"
                   class="form-control @error('nom') is-invalid @enderror" required>
            @error('nom')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
            <textarea id="description" name="description" rows="4" maxlength="255"
                      class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $event->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">255 caractères maximum.</div>
        </div>

        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="lieu" class="form-label">Lieu <span class="text-danger">*</span></label>
                <input type="text" id="lieu" name="lieu" value="{{ old('lieu', $event->lieu) }}"
                       class="form-control @error('lieu') is-invalid @enderror" required>
                @error('lieu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                <input type="date" id="date" name="date"
                       value="{{ old('date', $event->date?->format('Y-m-d')) }}"
                       class="form-control @error('date') is-invalid @enderror" required>
                @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>{{ $event->exists ? 'Enregistrer les modifications' : 'Créer l\'évènement' }}
    </button>
    <a href="{{ route('admin.evenements.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
