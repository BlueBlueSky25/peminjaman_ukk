<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';
    protected $primaryKey = 'pengembalian_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'peminjaman_id',
        'tanggal_kembali_aktual',
        'kondisi_alat',
        'persen_kerusakan',
        'keterlambatan_hari',
        'tarif_denda_per_hari',
        'denda_keterlambatan',
        'denda_kerusakan',
        'denda_kehilangan',
        'total_denda',
        'status_denda',
        'tanggal_bayar_denda',
        'diverifikasi_oleh',
        'keterangan_pembayaran',
        'keterangan',
        'jumlah_dikembalikan',
        'jumlah_bayar',
        'metode_pembayaran',
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'date',
        'tarif_denda_per_hari'   => 'decimal:2',
        'total_denda'            => 'decimal:2',
        'denda_kerusakan'        => 'decimal:2',
        'denda_kehilangan'       => 'decimal:2',
        'denda_keterlambatan'    => 'decimal:2',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id', 'peminjaman_id');
    }

    public function getRouteKeyName()
    {
        return 'pengembalian_id';
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh', 'user_id');
    }
}