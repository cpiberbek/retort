<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Release_packing extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'release_packings';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'jenis_kemasan',  'nama_produk', 'kode_produksi', 'expired_date', 'no_palet', 'release', 'keterangan',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    public function mincing()
    {
        return $this->belongsTo(Mincing::class, 'kode_produksi', 'uuid');
    }

    protected $dates = ['deleted_at'];
}