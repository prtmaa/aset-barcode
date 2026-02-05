<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'umur_manfaat' => 'integer',
    ];



    // Bulan terpakai
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
        if (!$this->tanggal_pembelian || !$this->umur_manfaat) {
            return null;
        }

        return $this->tanggal_pembelian
            ->copy()
            ->addMonths($this->umur_manfaat);
    }


    public function getIsDisposalAttribute()
    {
        return $this->nilai_buku <= 0;
    }

    public static function generateKodeByKategori($tipeId, $ignoreAssetId = null)
    {
        $tahun = date('Y');

        $tipe = DB::table('tipes')->where('id', $tipeId)->first();
        if (!$tipe) {
            throw new \Exception('Tipe tidak ditemukan');
        }

        // Ambil kode tipe
        $kodeTipe = property_exists($tipe, 'kode')
            ? $tipe->kode
            : strtoupper(substr(preg_replace('/\s+/', '', $tipe->nama), 0, 3));

        $prefix = $kodeTipe . '-' . $tahun;

        do {
            $last = DB::table('assets')
                ->where('tipe_id', $tipeId)
                ->when($ignoreAssetId, fn($q) => $q->where('id', '!=', $ignoreAssetId))
                ->where('kode_aset', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderBy('kode_aset', 'desc')
                ->value('kode_aset');

            $number = $last
                ? (int) substr($last, -4) + 1
                : 1;

            $newKode = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);

            $exists = DB::table('assets')->where('kode_aset', $newKode)->exists();
        } while ($exists);

        return $newKode;
    }
}
