<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentDownloadRequest;
use App\Http\Requests\DocumentFileUpdateRequest;
use App\Http\Resources\DocumentCollection;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentsFilesController extends Controller
{
    /**
     * Undocumented function
     *
     * @param  DocumentDownloadRequest $request
     * @param  string                  $document_number
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download(DocumentDownloadRequest $request, $document_number)
    {
        // ファイルのダウンロード
        $document = Document::where('document_number', '=', $document_number)->first();
        $headers = [['Content-Type' => $document->document_mime_type]];

        return Storage::download($document->document_path, $document->document_name, $headers);
    }

    /**
     * ファイルの更新
     *
     * @param  DocumentFileUpdateRequest $request
     * @param  string                    $document_number
     * @return void
     */
    public function updateFile(DocumentFileUpdateRequest $request, $document_number)
    {
        // ファイルの更新
        $document = Document::where('document_number', '=', $document_number)->first();
        $updatedDocument = DocumentFile::fileUpdate($request, $document);

        // ファイル名更新
        $document->document_name = $updatedDocument->documentName();
        $document->save();

        // レスポンス出力用オブジェクト取得
        $response = Document::where('documents.id', $document->id)->with('attributes')->get();
        return new DocumentCollection(DocumentResource::collection($response));
    }
}
