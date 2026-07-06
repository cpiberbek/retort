<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class RawMaterialInspection extends Model
{
    use HasFactory, SoftDeletes;

    // Karena Anda pakai guarded=['id'], maka kolom baru (plant_uuid, updated_by)
    // otomatis bisa diisi (Mass Assignment aman selama id dijaga).
    protected $guarded = ['id'];

    protected $casts = [
        'setup_kedatangan' => 'datetime',
        'mobil_check_warna' => 'boolean',
        'mobil_check_kotoran' => 'boolean',
        'mobil_check_aroma' => 'boolean',
        'mobil_check_kemasan' => 'boolean',
        'suhu_daging' => 'decimal:2',
        'analisa_ka_ffa' => 'float',
        'analisa_logo_halal' => 'boolean',
        'dokumen_halal_berlaku' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            if (Auth::check() && empty($model->created_by_uuid)) {
                $model->created_by_uuid = Auth::user()->uuid;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function productDetails(): HasMany
    {
        return $this->hasMany(InspectionProductDetail::class, 'raw_material_inspection_uuid', 'uuid');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_uuid', 'uuid');
    }

    /**
     * TAMBAHAN BARU: Relasi ke User pengupdate
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }
    public function inspectionDetails()
    {
        return $this->hasMany(InspectionProductDetail::class, 'raw_material_uuid', 'uuid');
    }
    public function details()
    {
        return $this->hasMany(InspectionProductDetail::class, 'raw_material_inspection_uuid', 'uuid');
    }
}
