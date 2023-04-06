<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    use HasFactory;

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id');
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
     * @param array $attributes
     * @return object
     */
    public static function getDocumentIdByAttributes(array $attributes)
    {
        $query = Attribute::join('documents', 'attributes.document_id', '=', 'documents.id');
        foreach ($attributes as $key => $values) {
            foreach ($values as $operator => $value) {
                $query->orWhere(function (Builder $query) use ($key, $operator, $value) {
                    $query->where('key', '=', $key);
                    if ($operator === '=') {
                        // 部分一致
                        $query->where('value', "LIKE", "%" . $value . "%");
                    } else {
                        $query->where('value', $operator, $value);
                    }
                });
            }
        }
        return $query->select('document_id')->get();
    }
}
