<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Œuvre de la galerie — table partagée avec le site WordPress.
 * La photo est stockée directement en base (colonne `image`, LONGBLOB).
 */
class Image extends Model
{
    use HasFactory;

    protected $table = 'bp_image';

    public $timestamps = false;

    /**
     * Colonnes hors blob : à utiliser dans les listes pour ne pas charger
     * toutes les photos en mémoire.
     */
    public const COLUMNS_WITHOUT_BLOB = ['id', 'nom', 'description', 'prix', 'shopify_variant_id'];

    protected $fillable = [
        'image',
        'nom',
        'description',
        'prix',
        'shopify_variant_id',
    ];

    protected $hidden = [
        'image',
    ];

    protected function casts(): array
    {
        return [
            'prix' => 'decimal:2',
        ];
    }

    public function location(): HasOne
    {
        return $this->hasOne(ImageLocation::class, 'image_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'bp_image_tags', 'image_id', 'tag_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'image_id');
    }

    /**
     * Recherche par nom ou description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, function (Builder $query) use ($term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('nom', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        });
    }

    /**
     * URL de la route qui sert la photo stockée en base.
     */
    public function photoUrl(): string
    {
        return route('admin.oeuvres.photo', $this);
    }
}
