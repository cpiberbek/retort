<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MasterPackaging extends Model
{
    use SoftDeletes;

    protected $table = 'master_packaging';

    protected $fillable = [
        'uuid',
        'plant_uuid',
        'nama_packaging',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}