<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

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
        $mimeRules = implode(",", config('mimetype.file_extension'));
        $mimeTypeRules = implode(",", config('mimetype.file_mime_types'));
        return [
            'file' => [
                'bail',
                'required',
                'file',
                'max:100000',
                'mimes:'. $mimeRules,
                'mimetypes:'. $mimeTypeRules,
            ],
            'attribute.*' => [
                'sometimes',
                'bail',
                'required',
                'string',
                'max:20',
                'attribute_name_register'
            ],
        ];
    }
}
