<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'kategori_id',
        'lokasi_id',
        'kondisi',
        'tanggal_pembelian',
        'foto',
        'catatan',
        'kelengkapan',
        'tipe_id',
        'vendor_id',
        'harga',
        'jumlah',
        'status',
    ];

    // Kategori aset
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Lokasi aset
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    // Spesifikasi aset (EAV)
    public function atributValues()
    {
        return $this->hasMany(AssetAttributeValue::class, 'asset_id');
    }

    // Riwayat pemakaian aset
    public function assignments()
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(AssetAssignment::class)
            ->where('status', 'aktif');
    }

    public function assignmentAktif()
    {
        return $this->hasOne(AssetAssignment::class)
            ->where('status', 'aktif');
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
