<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    protected $fillable = ['nama'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
