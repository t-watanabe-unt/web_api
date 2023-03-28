<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiCommonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションエラー時のレスポンス返却を指定
     *
     * @param  Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $response = response()->json(
            [
                'status' => 400,
                'message' => '入力値が不正です'
            ], 400
        );
        throw new HttpResponseException($response);
    }
}
