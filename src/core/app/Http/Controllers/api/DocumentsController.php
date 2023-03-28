<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentsController extends Controller
{
    public function create(DocumentUploadRequest $request)
    {
        $values = $request->all();
        Log::info($values);
        return response()->json($values, 200);
    }
}
