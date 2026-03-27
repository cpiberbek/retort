<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recall extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'recalls';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'penyebab', 'asal_informasi', 'jenis_pangan', 'nama_dagang', 'berat_bersih', 'jenis_kemasan', 'kode_produksi', 'tanggal_produksi', 'tanggal_kadaluarsa', 'no_pendaftaran', 'diproduksi_oleh', 'jumlah_produksi', 'jumlah_terkirim', 'jumlah_tersisa', 'tindak_lanjut', 'distribusi', 'neraca_penarikan', 'simulasi', 'total_waktu', 'evaluasi',
        'nama_manager', 'status_manager', 'catatan_manager', 'tgl_update_manager',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'distribusi'  => 'array',
        'neraca_penarikan'  => 'array',
        'simulasi'  => 'array',
        'evaluasi'  => 'array',
    ];
    protected $dates = ['deleted_at'];
}
