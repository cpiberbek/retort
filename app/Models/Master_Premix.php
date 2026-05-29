<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Master_Premix extends Model
{
    use SoftDeletes;

    protected $table = 'master_premixes';

    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'nama_premix',
        'kode_internal',
        'satuan',
        'plant_uuid',
        'created_by',
        'deleted_by'
    ];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function dataPlant()
    {
        return $this->belongsTo(Plant::class, 'plant_uuid', 'uuid');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }
}
