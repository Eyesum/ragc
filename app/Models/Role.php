<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public const ROLE_TYPE_ADMIN = 'admin';
    public const ROLE_TYPE_USER = 'user';

    public const ROLE_TYPES = [
        self::ROLE_TYPE_ADMIN => self::ROLE_TYPE_ADMIN,
        self::ROLE_TYPE_USER => self::ROLE_TYPE_USER,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

}
