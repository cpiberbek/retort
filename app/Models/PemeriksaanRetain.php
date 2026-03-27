<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PemeriksaanRetain extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hari',
        'tanggal',
        'keterangan',
        
        // Field Identitas & Lokasi
        'plant_uuid', 
        'created_by',
        'updated_by',
        
        // Field Verifikasi
        'status_spv',     
        'catatan_spv',    
        'verified_by',    
        'verified_at',    
    ];

    /**
     * Otomatis mengisi field system saat data dibuat atau diupdate
     */
    protected static function booted(): void
    {
        // Event saat Data Baru dibuat (Insert)
        static::creating(function ($model) {
            // 1. Generate UUID record jika kosong
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            
            // 2. Isi data otomatis dari User Login
            if (Auth::check()) {
                $user = Auth::user();

                // Isi created_by dengan UUID user (String)
                if (empty($model->created_by)) {
                    $model->created_by = $user->uuid; 
                }

                // Isi updated_by dengan UUID user (awal pembuatan)
                if (empty($model->updated_by)) {
                    $model->updated_by = $user->uuid;
                }

                // Isi plant_uuid dari kolom 'plant' di tabel users
                if (empty($model->plant_uuid)) {
                    $model->plant_uuid = $user->plant;
                }
            }
        });

        // Event saat Data Diupdate (Edit/Save)
        static::updating(function ($model) {
            if (Auth::check()) {
                // Update kolom updated_by dengan UUID user yang sedang login
                $model->updated_by = Auth::user()->uuid;
            }
        });
    }

    /**
     * Relasi ke Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PemeriksaanRetainItem::class, 'pemeriksaan_retain_id');
    }

    /**
     * Relasi: User Pembuat (Creator) 
     * Menggunakan 'uuid' di tabel users sebagai referensi, bukan 'id'
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }
    
    /**
     * Relasi: User Pengupdate (Updater)
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }

    /**
     * Relasi: User Verifikator
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by', 'uuid');
    }

    /**
     * Menggunakan UUID untuk Route Model Binding di URL
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $dates = ['deleted_at'];
}