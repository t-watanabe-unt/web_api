<?php

namespace App\Models;

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
}
