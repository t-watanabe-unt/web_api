<?php

namespace App\Http\Requests;

use App\Rules\DocumentAttributesNameRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class DocumentUploadRequest extends ApiCommonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
            ],
            'attribute.*' => [
                'sometimes',
                'bail',
                'required',
                'string',
                'max:20',
                new DocumentAttributesNameRule(),
            ],
        ];
    }
}
