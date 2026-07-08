<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Coordonnées GPS d'une œuvre — table partagée avec le site WordPress.
 */
class ImageLocation extends Model
{
    protected $table = 'bp_image_location';

    public $timestamps = false;

    protected $fillable = [
        'image_id',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }
}
