<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class DocumentFileUpdateRequest extends DocumentUploadRequest
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
        $rules = parent::rules();

        // 継承先のルールを削除
        unset($rules['attribute.*']);

        $rules['file'][] = 'file_extension';
        $rules['document_number'] = [
            'required',
            'string',
            'uuid',
            'exists:App\Models\Document,document_number'
        ];

        return $rules;
    }

    /**
     * 文書番号をマージ
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(
            [
                'document_number' => $this->route('document_number'),
            ]
        );
    }
}
