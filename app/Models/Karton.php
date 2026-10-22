<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karton extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kartons';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date', 'plant', 'nama_produk', 'kode_produksi', 'kode_karton',
        'waktu_mulai', 'waktu_selesai', 'jumlah', 'tgl_kedatangan',
        'nama_supplier', 'no_lot', 'keterangan',
        'nama_operator', 'status_operator', 'tgl_update_operator',
        'nama_koordinator', 'status_koordinator', 'tgl_update_koordinator',
        'username', 'username_updated', 'nama_spv', 'status_spv',
        'catatan_spv', 'tgl_update_spv', 'is_audit', 'source_uuid',
    ];

    // Relasi kode batch
    public function mincing()
    {
        return $this->belongsTo(Mincing::class, 'kode_produksi', 'uuid');
        // ->where('plant', Auth::user()->plant);
    }

    protected $dates = ['deleted_at'];

    // Audit casts
    protected $casts = [
        'is_audit' => 'boolean',
    ];

    // Relasi salinan audit dari data asli
    public function auditVersion()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')
            ->where('is_audit', true);
    }

    // Membuat salinan data untuk Audit Mode
    public function copyToAudit(): self
    {
        $clone = $this->replicate(['id']);
        $clone->is_audit = true;
        $clone->source_uuid = $this->uuid;
        $clone->save();

        return $clone;
    }
}
