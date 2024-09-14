<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipType extends Model
{
    use HasFactory;
    protected $table = 'membership_types';
    protected $fillable = [
        'name',
        'payment_period',
        'cost'
    ];

    /**
     * @return \Attribute
     */
    protected function cost(): \Attribute
    {
        return \Attribute::make(
            get: fn ($value) => number_format(($value / 100), 2, '.', ''),
            set: fn ($value) => ($value * 100),
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberships(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Membership::class);
    }
}
