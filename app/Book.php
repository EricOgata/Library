<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Book extends Model
{
    protected $guarded = [];

    public function path(){
        return '/books/' . $this->id;
    }

    public function checkout(User $user){
        $reservationCount = Reservation::activeReservationsByUser($user)->count();
        if($reservationCount <= 2){
            $this->reservations()->create([
                'user_id'           =>  $user->id,
                'checked_out_at'    =>  now(),
            ]);
        }
    }

    public function checkin(User $user) {
        $reservation = $this->reservations()->where('user_id', $user->id)
            ->whereNotNull('checked_out_at') // um livro emprestado possui checked_out_at, senão, é nulo
            ->whereNull('checked_in_at') // Um livro emprestado possui checked_in_at nulo.
            ->first(); // Retorna primeiro registro encontrado

        if (is_null($reservation)){
            throw new \Exception;
        }

        $reservation->update([
            'checked_in_at' =>  now(),
        ]);
        
    }

    public function setAuthorIdAttribute($author){
        /**
         * The firstOrCreate method will attempt to locate a database 
         * record using the given column / value pairs. 
         * If the model can not be found in the database, 
         * a record will be inserted with the attributes from the first parameter, 
         * along with those in the optional second parameter.
         */
        $this->attributes['author_id'] = (Author::firstOrCreate([
            'name' => $author
        ]))->id;
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }
}
