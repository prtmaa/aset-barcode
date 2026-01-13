<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nama',
        'email',
        'jabatan',
        'departemen',
        'no_hp'
    ];

    public function assetAssignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }
}
