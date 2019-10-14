<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $guarded = [];

    public static function activeReservationsByUser(User $user = null)
    {
        return Reservation::all()->where('user_id', $user->id)
            ->where('checked_out_at', '!=', null)
            ->where('checked_in_at', '=', null);
    }
}
