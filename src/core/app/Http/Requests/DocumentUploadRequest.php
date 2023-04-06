<?php

namespace App\Http\Requests;

use App\Rules\DocumentAttributesNameRule;
use App\Rules\DocumentFileMimeTypeRule;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use App\Constants\MimeTypesConstant;

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
        $mimes = implode(",", MimeTypesConstant::FILE_EXTENSIONS);
        $mimeRules = implode(",", MimeTypesConstant::FILE_MIME_TYPES);
        return [
            'file' => [
                'required',
                'file',
                'mimes:'. $mimes,
                'mimetypes:'. $mimeRules,
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
