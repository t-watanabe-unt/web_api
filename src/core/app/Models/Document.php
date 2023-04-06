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
}
