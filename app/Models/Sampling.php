<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sampling extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'samplings';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'jenis_sampel', 'jenis_kemasan',
        'nama_produk', 'kode_produksi', 'jumlah', 'jamur', 'lendir',
        'klip_tajam', 'pin_hole', 'air_trap_pvdc', 'air_trap_produk',
        'keriput', 'bengkok', 'non_kode', 'over_lap', 'kecil',
        'terjepit', 'double_klip', 'seal_halus', 'basah', 'dll',
        'catatan', 'username', 'username_updated', 'nama_spv',
        'status_spv', 'catatan_spv', 'tgl_update_spv',
        'is_audit', 'source_uuid',
    ];

    protected $casts = [
        'is_audit' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    public function auditVersion()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')
            ->where('is_audit', true);
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