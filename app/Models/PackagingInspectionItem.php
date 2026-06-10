<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Note: Nama file Anda 'PackagingInspectionitem.php', 
 * tapi nama classnya 'PackagingInspectionItem'.
 * Pastikan nama file di-rename menjadi 'PackagingInspectionItem.php' agar konsisten.
 */
class PackagingInspectionItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Otomatis mengisi UUID saat membuat record baru.
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'packaging_inspection_id',
        'packaging_type',
        'supplier',
        'lot_batch',
        
        // Field baru yang dipindah dari header
        'no_pol',
        'vehicle_condition',
        'pbb_op',

        // Field kondisi yang sekarang diisi dari UI
        'condition_design',
        'condition_sealing',
        'condition_color',
        'condition_dimension',
        'condition_weight_pcs', // Ini sudah ada di form lama (hidden), saya biarkan
        
        // Field Kuantitas
        'quantity_goods',
        'quantity_sample',
        'quantity_reject',
        'acceptance_status',
        'notes',
    ];

    /**
     * Mendapatkan data inspeksi induk.
     */
    public function inspection(): BelongsTo
    {
        return $this->belongsTo(PackagingInspection::class, 'packaging_inspection_id');
    }
}
