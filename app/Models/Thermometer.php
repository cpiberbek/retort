<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thermometer extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'thermometers';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'peneraan',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];
    protected $casts = [
        'peneraan'  => 'array',
    ];

    protected $dates = ['deleted_at'];
}

