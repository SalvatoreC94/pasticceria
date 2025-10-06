<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin'          => 'boolean',
    ];

    // Consente l'accesso al pannello solo agli admin
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }
}
