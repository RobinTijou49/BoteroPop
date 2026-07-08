@csrf

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header bg-white fw-semibold">Informations</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom', $image->nom) }}"
                           class="form-control @error('nom') is-invalid @enderror" required>
                    @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea id="description" name="description" rows="4" maxlength="256"
                              class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $image->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">256 caractères maximum.</div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prix" class="form-label">Prix (€)</label>
                        <input type="number" step="0.01" min="0" id="prix" name="prix"
                               value="{{ old('prix', $image->prix) }}"
                               class="form-control @error('prix') is-invalid @enderror">
                        @error('prix')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="shopify_variant_id" class="form-label">Shopify Variant ID</label>
                        <input type="text" id="shopify_variant_id" name="shopify_variant_id" maxlength="50"
                               value="{{ old('shopify_variant_id', $image->shopify_variant_id) }}"
                               class="form-control @error('shopify_variant_id') is-invalid @enderror">
                        @error('shopify_variant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" id="adresse" name="adresse" value="{{ old('adresse') }}"
                           class="form-control @error('adresse') is-invalid @enderror"
                           placeholder="ex. 12 rue de la Paix, 75002 Paris">
                    @error('adresse')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="bi bi-geo-alt me-1"></i>L'adresse n'est pas enregistrée : elle est convertie en
                        coordonnées GPS via OpenStreetMap Nominatim, stockées dans la localisation de l'œuvre.
                        Laissez vide pour conserver les coordonnées actuelles.
                    </div>
                </div>

                @if ($image->location)
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Latitude actuelle</label>
                            <input type="text" class="form-control" value="{{ $image->location->latitude }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Longitude actuelle</label>
                            <input type="text" class="form-control" value="{{ $image->location->longitude }}" disabled>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold">Photo</div>
            <div class="card-body">
                <img id="photo-preview"
                     src="{{ $image->exists ? $image->photoUrl() : '' }}"
                     alt="Aperçu de la photo"
                     class="img-fluid rounded mb-3 {{ $image->exists ? '' : 'd-none' }}">

                <input type="file" name="photo" data-preview="photo-preview"
                       accept=".jpg,.jpeg,.png,.webp"
                       class="form-control @error('photo') is-invalid @enderror"
                       @if(! $image->exists) required @endif>
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Formats acceptés : jpg, jpeg, png, webp — 10 Mo maximum.</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Tags</div>
            <div class="card-body">
                @php
                    $selectedTags = collect(old('tags', $image->tags->pluck('id')->all()))->map(fn ($id) => (int) $id);
                @endphp
                @forelse ($tags as $tag)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}"
                               id="tag-{{ $tag->id }}" @checked($selectedTags->contains($tag->id))>
                        <label class="form-check-label" for="tag-{{ $tag->id }}">{{ $tag->nom }}</label>
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        Aucun tag disponible.
                        <a href="{{ route('admin.tags.create') }}">Créer un tag</a>
                    </p>
                @endforelse
                @error('tags')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg me-1"></i>{{ $image->exists ? 'Enregistrer les modifications' : 'Créer l\'œuvre' }}
    </button>
    <a href="{{ route('admin.oeuvres.index') }}" class="btn btn-outline-secondary">Annuler</a>
</div>
