<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Withdrawl extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'withdrawls';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'no_withdrawl', 'nama_produk', 'kode_produksi', 'exp_date', 'jumlah_produksi', 'jumlah_edar', 'tanggal_edar', 'jumlah_tarik', 'tanggal_tarik', 'rincian', 
        'nama_manager', 'status_manager', 'catatan_manager', 'tgl_update_manager',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'rincian'  => 'array',
    ];
    protected $dates = ['deleted_at'];
}