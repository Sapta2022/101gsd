<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Minimal Phase 1 stub. The full Club model (Razorpay linked account,
 * stakeholders, KYC status, subscription) is built out in Phase 2/3 —
 * this exists now only so tenant scoping (club_id foreign keys, the
 * BelongsToClub trait) has a real table/model to bind against.
 */
class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
