<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Document;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentsController extends Controller
{
    /**
     * Documentモデルインスタンス
     *
     * @var object
     */
    private $document;

    /**
     * Attributeモデルインスタンス
     *
     * @var object
     */
    private $attribute;

    /**
     * 使用するモデルのインスタンスを生成
     *
     * @param Document  $document
     * @param Attribute $attribute
     */
    public function __construct(Document $document, Attribute $attribute)
    {
        $this->document = $document;
        $this->attribute = $attribute;
    }

    /**
     * 文書の作成
     *
     * @param  DocumentUploadRequest $request
     * @return void
     */
    public function create(DocumentUploadRequest $request)
    {
        $document = DB::transaction(
            function () use ($request) {
                $fileExtension = $request->file('file')->getClientOriginalExtension();
                $originalFileName = basename($request->file('file')->getClientOriginalName(), '.' . $fileExtension);
                $mimeType = $request->file('file')->getMimeType();
                $documentNumber = Str::uuid();
                $request->file('file')->storeAs('/public/', $documentNumber . '.' . $fileExtension);
                $document = $this->document->create($documentNumber, $originalFileName, $mimeType, $fileExtension);

                // 文書の属性の入力がある
                if ($request->has('attribute')) {
                    $this->attribute->create($document->id, $request->input('attribute'));
                }
                return $document;
            }
        );
        $documentResponse = $this->document->getDocumentByDocumentId($document->id, $request->has('attribute'));
        $response = $this->makeJsonResponseDocument($documentResponse, $request->has('attribute'));

        return response()->json($response, 200);
    }

    /**
     * 登録後のレスポンス生成
     *
     * @param  [type] $documentResponse
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

    public function search(Request $request)
    {

        // ドキュメントの検索
        return response()->json($request->query(), 200);
    }

    public function destroy()
    {
        // ドキュメントの削除
    }
}
