<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Packing extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'packings';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'waktu', 'kalibrasi', 'qrcode', 'kode_printing', 'kode_toples', 'kode_karton', 'suhu', 'speed', 'kondisi_segel', 'berat_toples', 'berat_pouch', 'no_lot', 'tgl_kedatangan', 'nama_supplier', 'keterangan',
        'nama_produksi', 'status_produksi', 'tgl_update_produksi',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}