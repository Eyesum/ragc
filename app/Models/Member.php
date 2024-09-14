<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $table = 'members';
    protected $fillable = [
        'user_id',
        'membership_number',
        'profile_picture',
        'first_name',
        'last_name',
        'date_of_birth',
        'contact_number',
        'address_line1',
        'address_line2',
        'city',
        'county',
        'postcode',
        'id_type_seen',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function juniorMembers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JuniorMember::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function emergencyContact(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EmergencyContact::class);
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
