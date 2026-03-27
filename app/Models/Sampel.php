<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sampel extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sampels';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'jenis_sampel', 'nama_produk', 'kode_produksi', 'keterangan',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

     protected $dates = ['deleted_at'];
}
