<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid; 

class List_form extends Model
{
    use HasFactory, HasUuid;
    
    protected $fillable = ['laporan', 'no_dokumen', 'last_revisi', 'last_updated', 'username', 'plant', 'uuid', 'standar_ref'];

    protected $casts = [ 'standar_ref' => 'array' ];
}
