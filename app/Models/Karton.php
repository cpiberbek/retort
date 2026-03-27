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
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'nama_produk', 'kode_produksi', 'kode_karton', 'waktu_mulai', 'waktu_selesai', 'jumlah', 'tgl_kedatangan', 'nama_supplier',  'no_lot',  'keterangan', 
        'nama_operator', 'status_operator', 'tgl_update_operator', 'nama_koordinator', 'status_koordinator', 'tgl_update_koordinator', 
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];
    
    //relasi kode batch     
    public function mincing()
    {
        return $this->belongsTo(Mincing::class, 'kode_produksi', 'uuid');
        // ->where('plant', Auth::user()->plant);  
    }

    protected $dates = ['deleted_at'];
}