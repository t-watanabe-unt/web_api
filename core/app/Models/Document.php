<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    /**
     * 文書idで取得
     *
     * @param  integer $documentId
     * @param  boolean $hasAttribute
     * @return object
     */
    public static function getDocumentByDocumentId(int $documentId, bool $hasAttribute)
    {
        $query = Document::where('documents.id', '=', $documentId);
        if ($hasAttribute) {
            $query->join('attributes', 'documents.id', '=', 'attributes.document_id');
        }
        $documentInfo = $query->get();

        return $documentInfo;
    }

    /**
     * 登録しているファイル拡張子と比較
     * 同一拡張子かをチェック
     *
     * @param object $value
     * @param string $documentNumber
     * @return boolean
     */
    public static function isValidFileExtension($value, $documentNumber)
    {
        // リクエストされた文書番号から登録されている文書を取得
        $document = Document::where('document_number', $documentNumber)->first();

        if ($document->document_extension !== $value->getClientOriginalExtension()) {
            return false;
        }

        return true;
    }
}
