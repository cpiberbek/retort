<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prepacking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'prepackings';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'nama_produk', 'kode_produksi', 'conveyor', 'berat_produk', 'suhu_produk', 'kondisi_produk', 'catatan', 
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'suhu_produk'  => 'array',
        'kondisi_produk'  => 'array',
        'berat_produk'  => 'array',
    ];
    
    protected $dates = ['deleted_at'];
}
