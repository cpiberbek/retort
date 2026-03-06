<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mincing extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'mincings';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'kode_produksi', 
        'waktu_mulai', 'waktu_selesai', 
        'premix', 'non_premix', 
        'daging', 
        'suhu_sebelum_grinding', 
        'waktu_mixing_premix',
        'waktu_bowl_cutter',  
        'waktu_aging_emulsi_awal', 'waktu_aging_emulsi_akhir', 
        'suhu_akhir_emulsi_gel', 
        'waktu_mixing',      
        'suhu_akhir_mixing', 'suhu_akhir_emulsi', 'catatan',
        'nama_produksi', 'status_produksi', 'tgl_update_produksi',
        'username', 'username_updated', 'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'premix' => 'array',
        'non_premix' => 'array',
        'suhu_sebelum_grinding' => 'array', 
    ];

    //relasi kode batch 
    public function stuffing()
    {
        return $this->hasMany(Stuffing::class);
    }

    public function pvdc()
    {
        return $this->hasMany(Pvdc::class);
    }

    public function wire()
    {
        return $this->hasMany(Wire::class);
    }

    protected $dates = ['deleted_at'];
}