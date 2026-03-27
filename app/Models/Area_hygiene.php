<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 
use Illuminate\Database\Eloquent\SoftDeletes;

class Area_hygiene extends Model
{
    use HasFactory, HasUuid, SoftDeletes;
    
    protected $fillable = ['area', 'username', 'plant', 'uuid'];

    protected $dates = ['deleted_at'];
}
