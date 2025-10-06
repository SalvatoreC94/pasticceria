<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['session_id', 'user_id'];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Recupera o crea il carrello legato alla sessione corrente.
     * Se l'utente è loggato, associa/aggiorna user_id.
     */
    public static function fromSession(): self
    {
        $sessionId = session()->getId();

        $cart = static::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => auth()->id()]
        );

        if (auth()->check() && $cart->user_id !== auth()->id()) {
            $cart->user_id = auth()->id();
            $cart->save();
        }

        return $cart;
    }
}
