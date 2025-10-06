<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
class Cart extends Model
{
    protected $fillable = ['session_id','user_id'];
    public function items(){ return $this->hasMany(CartItem::class); }

    public static function fromSession(): ?self {
        $sid = session()->getId();
        return self::firstOrCreate(['session_id'=>$sid], ['user_id'=>auth()->id()]);
    }
}

