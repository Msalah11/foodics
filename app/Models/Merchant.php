<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email'];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
