<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class MagnetTrapModel extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'magnet_traps';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'nama_produk',
        'kode_batch',
        'pukul',
        'jumlah_temuan',
        'status',
        'keterangan',
        'produksi_id',
        'engineer_id',
        'created_by',
        'plant_uuid',     
        'updated_by',     
        'status_spv',
        'catatan_spv',
        'verified_by_spv_uuid',
        'verified_at_spv',
    ];

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }

    /**
     * Relasi ke User untuk mengetahui pembuat data
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    public function produksi()
    {
        return $this->belongsTo(Operator::class, 'produksi_id');
    }

    public function engineer()
    {
        return $this->belongsTo(Operator::class, 'engineer_id');
    }
        
    // Opsional: Definisikan relasi ke model User (jika operator dan engineer ada di tabel users)
    // public function operator()
    // {
    //     return $this->belongsTo(User::class, 'produksi_id');
    // }

    // public function engineer()
    // {
    //     return $this->belongsTo(User::class, 'engineer_id');
    // }

    public function mincing()
    {
        return $this->belongsTo(Mincing::class, 'kode_batch', 'uuid');
        // ->where('plant', Auth::user()->plant);  
    }


}