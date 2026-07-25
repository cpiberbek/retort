<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoadingDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * Relasi belongsTo ke LoadingProduk.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
    public function loadingProduk()
    {
        // Pastikan 'loading_check_id' adalah nama foreign key yang benar
        return $this->belongsTo(LoadingProduk::class, 'loading_check_id'); 
    }
    
    // Fungsi getRouteKeyName() dihapus dari sini karena tidak diperlukan
}