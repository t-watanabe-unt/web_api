<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 文書の属性更新時のバリデーション
 */
class DocumentAttributesRequest extends ApiCommonRequest
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
            'document_number' => [
                'required',
                'string',
                'uuid',
                'exists:App\Models\Document,document_number'
            ],
            'key' => [
                'bail',
                'required',
                'attribute_name_route'
            ],
            'value' => [
                'bail',
                'required',
                'string',
                'max:20',
                'attribute_name_register'
            ],
        ];
    }

    /**
     * 文書番号と文書の属性のKeyをマージ
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(
            [
                'document_number' => $this->route('document_number'),
                'key' => $this->route('key'),
            ]
        );
    }
}
