<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuffing extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'stuffings';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'kode_produksi', 'exp_date', 'kode_mesin', 'jam_mulai', 'suhu', 'sensori', 'kecepatan_stuffing', 'panjang_pcs', 'berat_pcs', 'kebersihan_seal', 'kekuatan_seal', 'diameter_klip', 'print_kode', 'lebar_cassing', 'catatan', 
        'nama_produksi', 'status_produksi', 'tgl_update_produksi',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    //relasi kode batch     
    public function mincing()
    {
        return $this->belongsTo(Mincing::class, 'kode_produksi', 'uuid');
    }

    protected $dates = ['deleted_at'];
}