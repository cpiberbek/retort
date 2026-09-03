<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Models\Plant;

class Produk extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = ['nama_produk', 'username', 'plant', 'uuid', 'bahan_baku'];

    protected $casts = [
        'bahan_baku' => 'array',
    ];

    public function dataPlant()
    {
        // Parameter 2: foreign_key di table produks ('plant')
        // Parameter 3: owner_key di table plants ('uuid')
        return $this->belongsTo(Plant::class, 'plant', 'uuid');
    }
}
