<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'metals';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'pukul', 'fe', 'nfe', 'sus', 'nama_produksi', 'status_produksi', 'tgl_update_produksi', 'nama_engineer', 'status_engineer', 'tgl_update_engineer', 'catatan',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];
    protected $dates = ['deleted_at'];
}
