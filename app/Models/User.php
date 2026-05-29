<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, HasUuids, HasRoles, SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'uuid';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'uuid', 'name', 'username', 'password', 'plant',
        'department', 'type_user', 'photo', 'email',
        'activation', 'updater'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Jika pakai timestamp di database
    public $timestamps = true;

// app/Models/User.php
    public function plantRelasi()
    {
        return $this->belongsTo(Plant::class, 'plant', 'uuid');
    }

    public function departmentRelasi()
    {
        return $this->belongsTo(Departemen::class, 'department');
    }

    public const TYPE_USER_ROLE_MAP = [
        0 => 'admin',
        1 => 'manager',
        2 => 'supervisor',
        3 => 'foreman_produksi',
        8 => 'foreman_qc',
        4 => 'inspector',
        5 => 'engineer',
        6 => 'warehouse',
        7 => 'lab',
    ];

}
