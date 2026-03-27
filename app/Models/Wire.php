<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wire extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'wires';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'nama_supplier', 'data_wire', 'catatan', 
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];

    protected $casts = [
        'data_wire'  => 'array',
    ];

    //relasi kode batch 
    public function mincing()
    {
        return $this->belongsTo(Mincing::class);
    }

    protected $dates = ['deleted_at'];
}