<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productivity extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'productivities';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'hari_kerja',
        'plant',
        'tonase_bulanan',
        'total_manpower',
        'username',
        'username_updated',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $dates = ['deleted_at'];
}