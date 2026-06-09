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
        'date', 'plant', 'shift', 'nama_produk', 'kode_produksi', 'exp_date', 
        'data_stuffing', // <-- Tambahkan kolom JSON ini
        'nama_produksi', 'status_produksi', 'tgl_update_produksi',
        'username', 'username_updated', 'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    // Konversi JSON otomatis menjadi Array PHP bertenaga Eloquent
    protected $casts = [
        'data_stuffing' => 'array',
    ];

    public function mincing()
    {
        return $this->belongsTo(Mincing::class, 'kode_produksi', 'uuid');
    }

    protected $dates = ['deleted_at'];
}