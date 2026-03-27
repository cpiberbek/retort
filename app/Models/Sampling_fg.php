<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sampling_fg extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sampling_fgs';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'palet', 'nama_produk', 'kode_produksi', 'exp_date', 'pukul', 'kalibrasi', 'berat_produk', 'keterangan', 'isi_per_box', 'kemasan', 'jumlah_box', 'release', 'reject', 'hold', 'item_mutu', 'catatan', 'username', 'username_updated',  'nama_koordinator', 'status_koordinator', 'tgl_update_koordinator', 'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $dates = ['deleted_at'];
}

