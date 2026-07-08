<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Tag d'œuvre — table partagée avec le site WordPress.
 */
class Tag extends Model
{
    use HasFactory;

    protected $table = 'bp_tags';

    public $timestamps = false;

    protected $fillable = [
        'nom',
    ];

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'bp_image_tags', 'tag_id', 'image_id');
    }
}
