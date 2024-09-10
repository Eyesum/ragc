<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_id',
        'start_date',
        'renewal_date',
        'reminder_date',
        'paid_date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membership(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
