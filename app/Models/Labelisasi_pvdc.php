<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAudit;

class Labelisasi_pvdc extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAudit;

    protected $table = 'labelisasi_pvdcs';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'plant',
        'shift',
        'nama_produk',
        'labelisasi',
        'nama_operator',
        'status_operator',
        'tgl_update_operator',
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
        'labelisasi' => 'json',
        'is_audit' => 'boolean',
    ];
    
    public function getLabelisasiDetailAttribute()
    {

        $items = $this->labelisasi ?? [];

        if (!is_array($items)) {
            $items = json_decode($items, true) ?: [];
        }

        return collect($items)->map(function ($item) {
            $mincing = Mincing::where('uuid', $item['kode_batch'] ?? null)->first();

            return [
                'mesin'      => $item['mesin'] ?? null,
                'kode_batch' => $item['kode_batch'] ?? null,
                'file'       => $item['file'] ?? null,
                'keterangan' => $item['keterangan'] ?? null,
                'mincing'    => $mincing,
            ];
        });
    }

    protected $dates = ['deleted_at'];

}