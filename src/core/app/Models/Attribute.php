<?php

namespace App\Models;

use App\Constants\ValidationConstant;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'key',
        'value'
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    /**
     * 文書登録時、文書の属性のkeyが10文字以内
     * かつ、英数字で入力されている
     *
     * @param  string $attribute
     * @param  string $value
     * @return boolean
     */
    public static function isValidAttributeNameWithRegister($attribute, $value)
    {
        $attribute = preg_replace('/attribute./', '', $attribute);
        if (!preg_match('/^[a-zA-Z0-9]{1,10}+$/', $attribute) || empty($attribute)) {
            return false;
        }
        return true;
    }

    /**
     * 文書の属性のkeyが10文字以内
     * かつ、英数字で入力されている
     *
     * @param  string $attribute
     * @param  string $value
     * @return boolean
     */
    public static function isValidAttributeName($attribute, $value)
    {
        if (!preg_match('/^[a-zA-Z0-9]{1,10}+$/', $attribute) || empty($attribute)) {
            return false;
        }
        return true;
    }

    /**
     * 文書の属性のkeyが10文字以内
     * かつ、英数字で入力されている
     *
     * @param  string $attribute
     * @param  string $value
     * @return boolean
     */
    public static function isValidAttributeNameWithRoute($attribute, $value)
    {
        if (!preg_match('/^[a-zA-Z0-9]{1,10}+$/', $value) || empty($value)) {
            return false;
        }
        return true;
    }

    /**
     * 文書の属性を検索する比較演算子とVALUEのチェック
     *
     * @param  string $attribute
     * @param  array  $value
     * @return boolean
     */
    public static function isValidOperatorValue($attribute, $value)
    {
        // 比較演算子がない
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $key => $vl) {
            // 比較演算子の入力チェック
            if (!in_array($key, ValidationConstant::OPERATORS)) {
                return false;
            }

            // valueの入力数チェック
            if (ValidationConstant::VALUE_MAX < mb_strlen($vl) || empty($vl)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 属性の新規登録
     *
     * @param  integer $documentId
     * @param  array   $attributes
     * @return void
     */
    public function create(int $documentId,array $attributes)
    {
        $values = [];
        foreach ($attributes as $key => $value) {
            $values[] = [
                'document_id' => $documentId,
                'key' => $key,
                'value' => $value,
            ];
        }
        $this->insert($values);
    }

    /**
     * 文書の属性から対象のdocument_idを取得する
     *
     * @param  array $attributes
     * @return object
     */
    public static function getDocumentIdByAttributes(array $attributes)
    {
        $query = Attribute::join('documents', 'attributes.document_id', '=', 'documents.id');
        foreach ($attributes as $key => $values) {
            foreach ($values as $operator => $value) {
                $query->orWhere(
                    function (Builder $query) use ($key, $operator, $value) {
                        $query->where('key', '=', $key);
                        if ($operator === '=') {
                            // 部分一致
                            $query->where('value', "LIKE", "%" . $value . "%");
                        } else {
                            $query->where('value', $operator, $value);
                        }
                    }
                );
            }
        }
        return $query->select('document_id')->get();
    }
}
