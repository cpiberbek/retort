<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LoadingProduk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'loading_checks'; 
    protected $guarded = [];

    protected $casts = [
        'kondisi_mobil' => 'array',
        'is_audit' => 'boolean',
    ];

    /**
     * Boot function untuk menangani Event Model
     */
    protected static function boot()
    {
        parent::boot();

        // Event saat data BARU akan dibuat (Creating)
        static::creating(function ($model) {
            // 1. Generate UUID jika belum ada
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }

            // 2. Isi created_by dan plant_uuid otomatis dari User yang Login
            if (Auth::check()) {
                if (empty($model->created_by)) {
                    $model->created_by = Auth::id();
                }
                
                // ISI PLANT_UUID DARI USER LOGIN
                if (empty($model->plant_uuid)) {
                    // Asumsi kolom di tabel users bernama 'plant'
                    $model->plant_uuid = Auth::user()->plant ?? null; 
                }
            }
        });

        // Event saat data AKAN DIUPDATE (Updating)
        static::updating(function ($model) {
            if (Auth::check()) {
                // Isi updated_by otomatis saat ada perubahan data
                $model->updated_by = Auth::id();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function details()
    {
        return $this->hasMany(LoadingDetail::class, 'loading_check_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi opsional untuk melihat siapa yang mengupdate terakhir
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getJamMulaiAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : null;
    }

    public function getJamSelesaiAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : null;
    }

    public function auditVersion()
    {
        return $this->hasOne(self::class, 'source_uuid', 'uuid')->where('is_audit', true);
    }

    public function copyToAudit(): self
    {
        $clone = $this->replicate(['id', 'uuid']);
        $clone->is_audit    = true;
        $clone->source_uuid = $this->uuid;
        $clone->save();

        foreach ($this->details as $detail) {
            $copy = $detail->replicate(['id', 'uuid']);
            $copy->uuid = (string) \Illuminate\Support\Str::uuid();
            $copy->loading_check_id = $clone->id;
            $copy->save();
        }

        return $clone;
    }
}