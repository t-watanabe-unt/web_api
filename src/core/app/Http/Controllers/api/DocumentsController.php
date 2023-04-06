<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentSearchRequest;
use App\Http\Requests\DocumentUploadRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\Attribute;
use App\Models\DocumentFileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentsController extends Controller
{
    /**
     * 文書の作成
     *
     * @param  DocumentUploadRequest $request
     * @return void
     */
    public function register(DocumentUploadRequest $request)
    {
        $document = DB::transaction(
            function () use ($request) {
                // ファイル処理、保存
                $file = DocumentFileUpload::storeFile($request);

                // 文書の保存
                $documentModel = new Document();
                $documentModel->document_number = $file->documentNumber();
                $documentModel->document_name = $file->documentName();
                $documentModel->document_mime_type = $file->documentMimeType();
                $documentModel->document_extension = $file->documentExtension();
                $documentModel->document_path = $file->path();
                $documentModel->save();

                // 文書の属性の入力がある
                if ($request->has('attribute')) {
                    $attributeModel = new Attribute();
                    $attributeModel->create($documentModel->id, $request->input('attribute'));
                }
                return $documentModel;
            }
        );
        $response = Document::where('documents.id', $document->id)->with('attributes')->get();
        return new DocumentCollection(DocumentResource::collection($response));
    }

    /**
     * 文書の属性で文書を検索
     *
     * @param Request $request
     * @return void
     */
    public function search(DocumentSearchRequest $request)
    {
        // 空で入力された場合
        if (empty($request->query())) {
            abort(400);
        }

        // 検索結果を取得
        $documentIds = Attribute::getDocumentIdByAttributes($request->query());
        $responses = Document::whereIn('documents.id', collect($documentIds)->unique())->with('attributes')->get();
        return new DocumentCollection(DocumentResource::collection($responses));
    }

    public function destroy()
    {
        // ドキュメントの削除
    }
}
