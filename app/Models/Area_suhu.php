<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Area_suhu extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'username',
        'plant',
        'area',
        'standar_min',
        'standar_max',
        'rh_min',
        'rh_max'
    ];
}
