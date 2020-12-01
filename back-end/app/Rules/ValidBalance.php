<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Player;

class ValidBalance implements Rule
{
    protected $playerId; 

    /**
     * Create a new rule instance.
     * @param string $playerId
     * @return void
     */
    public function __construct($playerId)
    {
        $this->playerId = $playerId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $isValid = true;
        if (isset($this->playerId) && is_numeric($this->playerId)) {
            $balance = 1000;
            $player = Player::find($this->playerId);
            if ($player) {
                $balance = $player->balance;
            }
            $isValid = $value < $balance;
        }
        return $isValid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return [[
            'code' => 11,
            'message' => 'Insufficient balance'
        ]];
    }
}
