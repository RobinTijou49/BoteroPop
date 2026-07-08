<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Réservation d'œuvre — table propre au back office (non utilisée par WordPress).
 */
class Reservation extends Model
{
    use HasFactory;

    protected $table = 'bp_reservation';

    public $timestamps = false;

    public const STATUSES = [
        'en_attente' => 'En attente',
        'confirmee' => 'Confirmée',
        'annulee' => 'Annulée',
    ];

    protected $fillable = [
        'image_id',
        'customer_name',
        'email',
        'phone',
        'status',
        'reserved_at',
    ];

    protected function casts(): array
    {
        return [
            'reserved_at' => 'datetime',
        ];
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
