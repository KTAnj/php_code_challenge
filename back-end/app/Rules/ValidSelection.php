<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;

class ValidSelection implements Rule
{
    protected $stakeAmount;
    protected $message;
    const MAX_WIN_AMMOUNT = 20000;
    
    /**
     * Create a new rule instance.
     * 
     * @param  string $stakeAmount
     * @return void
     */
    public function __construct($stakeAmount)
    {
        $this->stakeAmount = $stakeAmount; 
        $this->message = [];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return  bool
     */
    public function passes($attribute, $value)
    {
        $isValid = true;
        if ($value && is_array($value)) {
            $selectionIds = array_column($value, 'id');
            $countedIds = array_count_values($selectionIds);
            $win_ammount = isset($this->stakeAmount) 
            && is_numeric($this->stakeAmount) ? $this->stakeAmount : 0;
            foreach ($value as $selection) {
                $errors = [];
                if (isset($selection['id'])) {
                    if ($countedIds[$selection['id']] > 1) {
                        $isValid = false;
                        $errors[] = [
                            'code' => 8, 
                            'message' => 'Duplicate selection found'];

                    } 

                    if (!array_key_exists("odds", $selection)
                        || preg_match(
                            "/^(\d*(.\d{1,3})?)$/", 
                            $selection['odds']
                        ) === 0
                    ) {
                        $isValid = false;
                        $errors[] = [
                            'code' => 1, 
                            'message' => 'Betslip structure mismatch'];
                        $this->message[] = [
                            'id' => $selection['id'],
                            'errors' => $errors
                        ];
                        continue;
                    } elseif ($selection['odds'] < 1) {
                        $isValid = false;
                        $errors[] = [
                            'code' => 6, 
                            'message' => 'Minimum odds are 1'];
                        $this->message[] = [
                            'id' => $selection['id'],
                            'errors' => $errors
                        ];
                        continue;
                    } elseif ($selection['odds'] > 10000) {
                        $isValid = false;
                        $errors[] = [
                            'code' => 7, 
                            'message' => 'Maximum odds are 10000'];
                        $this->message[] = [
                            'id' => $selection['id'],
                            'errors' => $errors
                        ];
                        continue;
                    } else {
                        $win_ammount *= $selection['odds'];
                    }
                }
                
                if ($errors) {
                    $this->message[] = [
                        'id' => $selection['id'],
                        'errors' => $errors
                    ];
                }
                
            }
            if ($win_ammount > self::MAX_WIN_AMMOUNT) {
                $isValid = false;
                $this->message[] = [
                    'code' => 9, 
                    'message' => 'Maximum win amount is '.self::MAX_WIN_AMMOUNT];
            }
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
        return $this->message;
    }
}
