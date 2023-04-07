<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentDownloadRequest;
use App\Models\Document;
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

    public function updateFile()
    {
        // ファイルの更新
    }
}
