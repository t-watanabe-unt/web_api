<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $values = [
            "document_number" => $this->document_number,
            "document_name" => $this->document_name,
            "document_mime_type" => $this->document_mime_type,
        ];
        if (isset($this->attributes)) {
            $values = array_merge($values, $this->reformatData());
        }
        return $values;
    }

    /**
     * レスポンス整形(文書番号に紐づく文書の属性でまとめる)
     *
     * @return array
     */
    private function reformatData()
    {
        $attributes = [];
        foreach ($this->attributes as $values) {
            $attributes[$values['key']] = $values['value'];
        }
        return $attributes;
    }
}
