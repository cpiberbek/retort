<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Traceability extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'traceabilities'; 

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'penyebab', 'asal_informasi', 'jenis_pangan', 'nama_dagang', 'berat_bersih', 'jenis_kemasan', 'kode_produksi', 'tanggal_produksi', 'tanggal_kadaluarsa', 'no_pendaftaran', 'jumlah_produksi', 'tindak_lanjut', 'persetujuan_trace', 'kelengkapan_form', 'total_waktu', 'kesimpulan',
        'nama_manager', 'status_manager', 'catatan_manager', 'tgl_update_manager',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'kelengkapan_form'  => 'array',
    ];
}