<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'name',
        'relationship',
        'address_line1',
        'address_line2',
        'city',
        'county',
        'postcode',
        'contact_number'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
       return $this->belongsTo(Member::class);
    }
}
