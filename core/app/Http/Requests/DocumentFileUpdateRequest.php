<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        $rules = [];
        $rules['document_number'] = [
            'bail',
            'required',
            'string',
            'uuid',
            'exists:App\Models\Document,document_number'
        ];
        $parentRules = parent::rules();

        // 継承先のルールを削除
        unset($parentRules['attribute.*']);

        $rules = array_merge($rules, $parentRules);
        $rules['file'][] = 'file_extension';
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
