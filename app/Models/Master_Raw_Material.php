<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Plant; // Pastikan model Plant di-import

class Master_Raw_Material extends Model
{
    use SoftDeletes;

    protected $table = 'master_raw_materials';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = ['nama_bahan_baku', 'plant_uuid', 'created_by', 'deleted_by', 'kode_internal', 'satuan'];

    public function getRouteKeyName() {
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

    // --- PERBAIKAN RELASI DISINI ---
    public function dataPlant()
    {
        // Mengikuti standar: foreign_key 'plant_uuid', owner_key 'uuid'
        return $this->belongsTo(Plant::class, 'plant_uuid', 'uuid');
    }
    public function creator() {
        return $this->belongsTo(User::class, 'created_by', 'uuid');
    }
}