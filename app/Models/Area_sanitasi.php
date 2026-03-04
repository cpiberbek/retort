<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 

class Area_sanitasi extends Model
{
    use HasFactory, HasUuid;
    
    protected $fillable = ['area', 'sub_area', 'bagian', 'username', 'plant', 'uuid'];

    protected $casts = [
        'bagian'  => 'array',
    ];
}
