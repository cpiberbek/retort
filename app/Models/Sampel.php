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
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv',
        'is_audit', 'source_uuid',
    ];

    protected $casts = [
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function auditVersion()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')->where('is_audit', true);
    }

    public function copyToAudit(): self
    {
        $clone = $this->replicate(['id']);
        $clone->is_audit    = true;
        $clone->source_uuid = $this->uuid;
        $clone->save();

        return $clone;
    }
}