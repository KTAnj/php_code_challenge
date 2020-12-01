<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\ValidSelection;
use App\Rules\ValidBalance;
use Illuminate\Validation\Rule;

class BetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'player_id' => [
                'required',
                'integer',
                Rule::unique('bet', 'player_id')
                    ->where(
                        function ($query) {
                            return $query->where('ended', 0);
                        }
                    )],
            'stake_amount' => [
                'required',
                'regex:/^(\d*(.\d{1,2})?)$/',
                'numeric',
                'min:0.3',
                'max:10000',
                new ValidBalance($this->input('player_id'))],
            'selections' => [
                'present',
                'array',
                'min:1',
                'max:2',
                new ValidSelection($this->input('stake_amount'))
            ],
            'selections.*.id' => 'required|integer',
            'selections.*.odds' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'required' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch'
            ],
            'player_id.integer' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch'],
            'player_id.unique' => [
                    'code' => 10, 
                    'message' => 'Your previous action is not finished yet'],
            'stake_amount.regex' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch'
            ],
            'stake_amount.numeric' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch'
            ],
            'stake_amount.min' => [ 
                'numeric' => [
                'code' => 2, 
                'message' => 'Minimum stake amount is :min']
            ],
            'stake_amount.max' => [
                'numeric' => [
                'code' => 3, 
                'message' => 'Maximum stake amount is :max']
            ],
            'selections.present' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch'
            ],
            'selections.array' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch'
            ],
            'selections.min' => [
                'array' => [
                'code' => 4, 
                'message' => 'Minimum number of selections is :min']
            ],
            'selections.max' => [
                'array' => [
                'code' => 5, 
                'message' => 'Maximum number of selections is :max']
                ],
            'selections.*.id.integer' => [
                'code' => 1, 
                'message' => 'Betslip structure mismatch']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        $globalErros = [];
        $sectionErrors = [];
        foreach ($errors as $k=>$error) {
            foreach ($error as $e) {
                if (isset($e['code'])) {
                    $globalErros[$e['code']] = $e;
                } else {
                    $sectionErrors[] = $e;
                }
            }
        }
       
        throw new HttpResponseException(
            response()->json(
                ['errors' => array_values($globalErros), 
                'selections' => $sectionErrors], JsonResponse::HTTP_BAD_REQUEST
            )
        );
    }
}
