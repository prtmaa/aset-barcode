<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'employee_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan'
    ];

    protected $dates = [
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
