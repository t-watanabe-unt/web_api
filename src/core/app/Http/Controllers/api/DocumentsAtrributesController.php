<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentAttributeDeleteRequest;
use App\Http\Requests\DocumentAttributesRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\Attribute;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentsAtrributesController extends Controller
{
    /**
     * 文書の属性の新規登録or更新
     *
     * @param  DocumentAttributesRequest $request
     * @param  string                    $document_number
     * @param  string                    $key
     * @return void
     */
    public function updateAttribute(DocumentAttributesRequest $request, $document_number, $key)
    {
        $document = Document::where('document_number', '=', $document_number)->first();

        // 文書の属性をkeyが存在しなければ新規登録、存在すれば更新
        $attributeModel = new Attribute();
        $attributeModel->where('document_id', $document->id);
        $attributeModel->updateOrCreate(
            ['key' => $key], // document_idで絞り込み、keyで検索
            [
                'document_id' => $document->id,
                'key' => $key,
                'value' => $request->input('value'),
            ]
        );
        $response = Document::where('documents.id', $document->id)->with('attributes')->get();
        return new DocumentCollection(DocumentResource::collection($response));
    }

    /**
     * 文書の属性を削除
     *
     * @param  DocumentAttributeDeleteRequest $request
     * @param  string                         $document_number
     * @param  string                         $key
     * @return void
     */
    public function destroy(DocumentAttributeDeleteRequest $request, $document_number, $key)
    {
        $document = Document::where('document_number', '=', $document_number)->first();

        // 削除対象のレコードを抽出
        $attributeModel = new Attribute();
        $attribute = $attributeModel->where(
            [
                'document_id' => $document->id,
                'key' => $key
            ]
        )->first();
        $attribute->delete();

        return response()->json([], 204);
    }
}
