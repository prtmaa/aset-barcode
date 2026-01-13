<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetAttribute extends Model
{
    protected $fillable = [
        'kategori_id',
        'nama_atribut',
        'tipe_input',
        'opsi',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function values()
    {
        return $this->hasMany(AssetAttributeValue::class);
    }
}
