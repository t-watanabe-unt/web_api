<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DocumentsCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 検索結果なしの場合
        if (empty($this->count())) {
            return [
                'message' => __('messages.response.empty'),
            ];
        }

        return $this->collection->map->toArray($request)->all();
    }
}
