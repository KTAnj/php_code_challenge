<?php

namespace App\Repositories;

use App\Player;

class PlayerRepository implements PlayerRepositoryInterface 
{
    public function find($id)
    {
        return Player::find($id);
    }
}