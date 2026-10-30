<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagingInspection extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected $fillable = [
        'inspection_date',
        'shift',

        // --- FIELD BARU (Identity & Location) ---
        'plant_uuid',
        'created_by',
        'updated_by',

        // Field Verifikasi
        'status_spv',
        'catatan_spv',
        'verified_by',
        'verified_at',

        'is_audit',
        'source_uuid',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'verified_at' => 'datetime',
        'is_audit' => 'boolean',
    ];

    /**
     * Relasi ke Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PackagingInspectionItem::class);
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

        foreach ($this->items as $item) {
            $copy = $item->replicate(['id', 'uuid']);
            $copy->packaging_inspection_id = $clone->id;
            $copy->save();
        }

        return $clone;
    }

    /**
     * Relasi ke User Verifikator
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi ke User Pembuat (Creator)
     */
    public function creator(): BelongsTo
    {
        // Parameter 2: Foreign key di table ini ('created_by')
        // Parameter 3: Owner key di table user ('uuid') -> INI PENTING
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }

    /**
     * Relasi ke User Pengupdate
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'uuid');
    }
}