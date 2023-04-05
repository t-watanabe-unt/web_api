<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class);
    }

    /**
     * 文書の登録
     *
     * @param  string $documentNumber
     * @param  string $originalFileName
     * @param  string $mimeType
     * @param  string $fileExtension
     * @return void
     */
    public function create(string $documentNumber,string $originalFileName,string $mimeType,string $fileExtension)
    {
        $this->document_number = $documentNumber;
        $this->document_name = $originalFileName;
        $this->document_mime_type = $mimeType;
        $this->document_extension = $fileExtension;
        $this->save();
        return $this;
    }

    /**
     * 文書idで取得
     *
     * @param  integer $documentId
     * @param  boolean $hasAttribute
     * @return object
     */
    public function getDocumentByDocumentId(int $documentId, bool $hasAttribute)
    {
        $query = $this->where('documents.id', '=', $documentId);
        if ($hasAttribute) {
            $query->join('attributes', 'documents.id', '=', 'attributes.document_id');
        }
        $documentInfo = $query->get();

        return $documentInfo;
    }
}
