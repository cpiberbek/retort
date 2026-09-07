<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PemeriksaanKekuatanMagnetTrap extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemeriksaan_kekuatan_magnet_traps';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'parameter_sesuai' => 'boolean',
        'verified_at' => 'datetime',
        'kekuatan_median_1' => 'decimal:2',
        'kekuatan_median_2' => 'decimal:2',
        'kekuatan_median_3' => 'decimal:2',
        'kekuatan_median_4' => 'decimal:2',
        'kekuatan_median_5' => 'decimal:2',
        'kekuatan_median_6' => 'decimal:2',
    ];

    /**
     * Boot method untuk otomatis mengisi uuid, plant_uuid, created_by, dan updated_by
     */
    protected static function boot()
    {
        parent::boot();

        // 1. Event saat data AKAN dibuat (Creating)
        static::creating(function ($model) {
            // Isi UUID jika kosong
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            // Jika user login, isi plant_uuid dan created_by otomatis
            if (Auth::check()) {
                $user = Auth::user();
                $model->plant_uuid = $user->plant; // Pastikan kolom 'plant' ada di user

                if (empty($model->created_by)) {
                    $model->created_by = $user->uuid;
                }
            }
        });

        // 2. Event saat data AKAN diupdate (Updating)
        static::updating(function ($model) {
            if (Auth::check()) {
                // Isi updated_by setiap kali ada perubahan data
                $model->updated_by = Auth::user()->uuid;
            }
        });
    }

    // --- RELASI ---

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    // Tambahan relasi updater
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by', 'uuid');
    }
}
