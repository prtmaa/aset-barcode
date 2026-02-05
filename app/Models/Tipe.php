<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'kode'];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
