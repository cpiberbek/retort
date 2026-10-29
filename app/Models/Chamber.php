<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAudit;

class Chamber extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAudit;

    protected $table = 'chambers';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'plant',
        'shift',
        'verifikasi',
        'catatan',
        'nama_operator',
        'status_operator',
        'tgl_update_operator',
        'nama_produksi',
        'status_produksi',
        'tgl_update_produksi',
        'username',
        'username_updated',
        'nama_spv',
        'status_spv',
        'catatan_spv',
        'tgl_update_spv',
        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'verifikasi' => 'array',
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];
}