<?php

// app/Models/InspectionProductDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class InspectionProductDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke data inspeksi utama
     */
    public function rawMaterialInspection(): BelongsTo
    {
        return $this->belongsTo(RawMaterialInspection::class);
    }

    public function inspection()
    {
        return $this->belongsTo(
            RawMaterialInspection::class,
            'raw_material_inspection_uuid',
            'uuid'
        );
    }
}
