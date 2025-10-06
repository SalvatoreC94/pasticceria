<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name','slug','description','is_visible'];
    public function products(){ return $this->hasMany(Product::class); }
    public function getRouteKeyName(): string { return 'slug'; }

}

