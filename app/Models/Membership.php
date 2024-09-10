<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_type_id',
        'joined_date',
        'status'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membershipType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function membershipRenewals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MembershipRenewal::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'member_type', 'member_id');
    }
}
