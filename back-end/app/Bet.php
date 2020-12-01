<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bet';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['stake_amount', 'player_id'];
}
