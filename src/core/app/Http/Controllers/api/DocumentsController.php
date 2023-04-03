<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Models\Document;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
     * @param Document $document
     * @param Attribute $attribute
     */
    public function __construct(Document $document, Attribute $attribute)
    {
        $this->document = $document;
        $this->attribute = $attribute;
    }

    public function create(DocumentUploadRequest $request)
    {
        $values = $request->all();
        Log::info($values);
        return response()->json($values, 200);
    }

    public function search()
    {
        // ドキュメントの検索
    }

    public function destroy()
    {
        // ドキュメントの削除
    }
}
