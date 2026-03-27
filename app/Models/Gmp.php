<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gmp extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'gmps';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'area', 'pemeriksaan',
        'username', 'nama_produksi', 'status_produksi',  'nama_spv', 'status_spv', 'catatan_spv', 
        'username_updated', 'tgl_update_produksi', 'tgl_update_spv', 'plant'
    ];

    protected $casts = [
        'pemeriksaan'  => 'array',
    ];

    protected $dates = ['deleted_at'];
}
