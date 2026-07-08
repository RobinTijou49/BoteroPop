<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Inscription à un évènement — table partagée avec le site WordPress.
 */
class Inscription extends Model
{
    use HasFactory;

    protected $table = 'bp_inscription';

    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'id_event',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'id_event');
    }

    public function fullName(): string
    {
        return trim($this->prenom.' '.$this->nom);
    }
}
