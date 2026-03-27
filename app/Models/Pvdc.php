<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pvdc extends Model
{
    use HasFactory, HasUuids,SoftDeletes;

    protected $table = 'pvdcs';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'nama_supplier', 'tgl_kedatangan', 'tgl_expired', 'data_pvdc', 'catatan', 
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'data_pvdc'  => 'array',
    ];

    public function getPvdcDetailAttribute()
    {
        $items = $this->data_pvdc ?? [];

        // Pastikan selalu array
        if (!is_array($items)) {
            $items = json_decode($items, true) ?: [];
        }
        return collect($items)->map(function ($item) {
            $detail = $item['detail'] ?? [];
            if (!is_array($detail)) {
                $detail = json_decode($detail, true) ?: [];
            }
            return [
                'mesin'  => $item['mesin'] ?? null,
                'detail' => collect($detail)->map(function ($d) {
                    $mincing = Mincing::where('uuid', $d['batch'] ?? null)->first();
                    return [
                        'batch'    => $d['batch'] ?? null,
                        'no_lot'   => $d['no_lot'] ?? null,
                        'waktu'    => $d['waktu'] ?? null,
                        'mincing'  => $mincing,
                    ];
                }),
            ];
        });
    }

    protected $dates = ['deleted_at'];
}