<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
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
        $documentResponse = Document::getDocumentByDocumentId($document->id, $request->has('attribute'));
        $response = $this->makeJsonResponseDocument($documentResponse, $request->has('attribute'));

        return response()->json($response, 200);
    }

    /**
     * 登録後のレスポンス生成
     *
     * @param  object $documentResponse
     * @return void
     */
    private function makeJsonResponseDocument(object $documentResponse, bool $hasAttribute)
    {
        $response = [
            "document_number" => $documentResponse[0]->document_number,
            "document_name" => $documentResponse[0]->document_name,
            "document_mime_type" => $documentResponse[0]->document_mime_type,
        ];
        if ($hasAttribute) {
            foreach ($documentResponse as $value) {
                $response[$value->key] = $value->value;
            }
        }
        return $response;
    }

    /**
     * 文書の属性で文書を検索
     *
     * @param Request $request
     * @return void
     */
    public function search(Request $request)
    {
        $documentIds = Attribute::getDocumentIdByAttributes($request->query());
        $responses = [];

        foreach (collect($documentIds)->unique() as $documentIds) {
            $document = Document::getDocumentByDocumentId($documentIds['document_id'], true);
            $responses[] = $this->makeJsonResponseDocument($document, true);
        }

        // 検索対象がない場合
        if (empty($responses)) {
            $responses = [
                'message' => __('messages.response.empty'),
            ];
        }
        return response()->json($responses, 200);
    }

    public function destroy()
    {
        // ドキュメントの削除
    }
}
