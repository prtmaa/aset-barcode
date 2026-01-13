<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetAttributeValue extends Model
{
    protected $fillable = [
        'asset_id',
        'asset_attribute_id',
        'nilai'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function atribut()
    {
        return $this->belongsTo(
            AssetAttribute::class,
            'asset_attribute_id'
        );
    }
}
