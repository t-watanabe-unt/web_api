<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 文書の削除時のバリデーション
 */
class DocumentDeleteRequest extends ApiCommonRequest
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
                'bail',
                'required',
                'string',
                'uuid',
                'exists:App\Models\Document,document_number'
            ]
        ];
    }

    /**
     * ルートパラメータをマージ
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
