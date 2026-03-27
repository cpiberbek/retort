<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Washing extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'washings';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'kode_produksi', 'pukul', 'panjang_produk', 'diameter_produk', 'airtrap', 'lengket', 'sisa_adonan', 'kebocoran', 'kekuatan_seal', 'print_kode', 'konsentrasi_pckleer', 'suhu_pckleer_1', 'suhu_pckleer_2', 'ph_pckleer', 'kondisi_air_pckleer', 'konsentrasi_pottasium', 'suhu_pottasium', 'ph_pottasium', 'kondisi_pottasium', 'suhu_heater', 'speed_1', 'speed_2', 'speed_3', 'speed_4', 'catatan', 
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