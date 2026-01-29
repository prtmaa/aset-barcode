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

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'harga' => 'float',
        'umur_manfaat' => 'float',
    ];



    // Bulan terpakai (maks 36)
    public function getBulanTerpakaiAttribute()
    {
        if (!$this->tanggal_pembelian) {
            return 0;
        }

        return min(
            $this->umur_manfaat,
            $this->tanggal_pembelian->diffInMonths(now())
        );
    }

    // Depresiasi per bulan
    public function getDepresiasiBulananAttribute()
    {
        return $this->harga / $this->umur_manfaat;
    }

    // Total depresiasi
    public function getTotalDepresiasiAttribute()
    {
        return $this->depresiasi_bulanan * $this->bulan_terpakai;
    }

    // Nilai buku
    public function getNilaiBukuAttribute()
    {
        return max(0, $this->harga - $this->total_depresiasi);
    }

    public function getTanggalDisposalAttribute()
    {
        if (!$this->tanggal_pembelian) {
            return null;
        }

        return $this->tanggal_pembelian->copy()->addMonths(36);
    }

    public function getIsDisposalAttribute()
    {
        return $this->nilai_buku <= 0;
    }
}
