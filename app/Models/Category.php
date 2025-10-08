<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name','slug','is_visible'];
    protected $casts = ['is_visible' => 'boolean'];

    public function products() {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }
}
