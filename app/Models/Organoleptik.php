<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organoleptik extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'organoleptiks';

    protected $primaryKey = 'uuid';  

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'date', 'plant', 'shift', 'nama_produk', 'sensori',
        'username', 'username_updated',  'nama_spv', 'status_spv', 'catatan_spv', 'tgl_update_spv'
    ];
    protected $casts = [
        'sensori'  => 'array',
    ];

    public function getOrganoleptikDetailAttribute()
    {
        $items = $this->sensori ?? [];

        if (!is_array($items)) {
            $items = json_decode($items, true) ?: [];
        }

        return collect($items)->map(function ($item) {
            $mincing = Mincing::where('uuid', $item['kode_produksi'] ?? null)->first();

            return [
                'kode_produksi' => $item['kode_produksi'] ?? null,
                'penampilan'   => $item['penampilan'] ?? null,
                'aroma'        => $item['aroma'] ?? null,
                'kekenyalan'   => $item['kekenyalan'] ?? null,
                'rasa_asin'    => $item['rasa_asin'] ?? null,
                'rasa_gurih'   => $item['rasa_gurih' ] ?? null,
                'rasa_manis'   => $item['rasa_manis'] ?? null,
                'rasa_daging'  => $item['rasa_daging'] ?? null,
                'rata_score'   => $item['rata_score'] ?? null,
                'release'      => $item['release'] ?? null,
                'mincing'      => $mincing,
            ];
        });
    }

    protected $dates = ['deleted_at'];

}