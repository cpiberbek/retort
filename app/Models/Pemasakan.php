<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasakan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pemasakans';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'kode_produksi', 'no_chamber', 'cooking', 'berat_produk', 'suhu_produk', 'jumlah_tray', 'total_reject', 'catatan', 
        'nama_produksi', 'status_produksi', 'tgl_update_produksi',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'cooking'  => 'array',
        'kode_produksi'  => 'array',
        'jumlah_tray'  => 'array',
    ];

    protected $dates = ['deleted_at'];
}