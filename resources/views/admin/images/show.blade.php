@extends('layouts.admin')

@section('title', $image->nom)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <a href="{{ route('admin.oeuvres.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour aux œuvres
        </a>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.oeuvres.edit', $image) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>Modifier
            </a>
            <button type="button" class="btn btn-outline-danger js-confirm-delete"
                    data-action="{{ route('admin.oeuvres.destroy', $image) }}"
                    data-label="l'œuvre « {{ $image->nom }} »">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-5">
            <div class="card">
                <img src="{{ $image->photoUrl() }}" alt="{{ $image->nom }}" class="card-img-top">
                <div class="card-body">
                    <h2 class="h5 mb-1">{{ $image->nom }}</h2>
                    <div class="fs-4 fw-bold text-primary">
                        {{ $image->prix !== null ? number_format($image->prix, 2, ',', ' ').' €' : 'Prix non renseigné' }}
                    </div>
                    <div class="mt-2">
                        @foreach ($image->tags as $tag)
                            <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $tag->nom }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold">Détails</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Description</dt>
                        <dd class="col-sm-8">{{ $image->description }}</dd>

                        <dt class="col-sm-4">Coordonnées GPS</dt>
                        <dd class="col-sm-8">
                            @if ($image->location)
                                <i class="bi bi-geo-alt text-success me-1"></i>{{ $image->location->latitude }}, {{ $image->location->longitude }}
                                <a href="https://www.openstreetmap.org/?mlat={{ $image->location->latitude }}&mlon={{ $image->location->longitude }}#map=17/{{ $image->location->latitude }}/{{ $image->location->longitude }}"
                                   target="_blank" rel="noopener" class="ms-2 small">
                                    Voir sur la carte <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4">Shopify Variant ID</dt>
                        <dd class="col-sm-8 mb-0">{{ $image->shopify_variant_id ?: '—' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
