<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueComplain extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'issue_complain';

    protected $primaryKey = 'uuid';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'judul_isu',
        'jenis',
        'detail',
        'plant',
        'username',
        'username_updated',
    ];

    protected $dates = ['deleted_at'];
}