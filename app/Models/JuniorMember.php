<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JuniorMember extends Model
{
    use HasFactory;
    protected $table = 'junior_members';
    protected $fillable = [
        'member_id',
        'membership_number',
        'profile_picture',
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function membership(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Membership::class, 'membership', 'member_type', 'member_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function scores(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Score::class, 'scores', 'member_type', 'member_id');
    }
}
