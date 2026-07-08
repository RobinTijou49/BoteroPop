<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Évènement — table partagée avec le site WordPress.
 */
class Event extends Model
{
    use HasFactory;

    protected $table = 'bp_event';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'description',
        'lieu',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'id_event');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', today())->orderBy('date');
    }
}
