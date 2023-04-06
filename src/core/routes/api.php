<?php

use App\Http\Controllers\api\DocumentsAtrributesController;
use App\Http\Controllers\api\DocumentsController;
use App\Http\Controllers\api\DocumentsFilesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 文書
Route::prefix('v1')->group(
    function () {
        Route::name('documents.')->group(
            function () {
                Route::prefix('documents')->group(
                    function () {
                        // 文書の登録
                        Route::post('/', [DocumentsController::class, 'register'])->name('register');
                        // 文書の検索
                        Route::get('/', [DocumentsController::class, 'search'])->name('search');
                        // 文書の削除
                        Route::delete('/{document_number}', [DocumentsController::class, 'destroy'])->name('destroy');

                        // 文書の属性の更新
                        Route::patch('/{document_number}/attributes', [DocumentsAtrributesController::class, 'updateAttribute'])->name('updateAttribute');

                        // 文書ファイルの取得(DL)
                        Route::get('/{document_number}/file', [DocumentsFilesController::class, 'download'])->name('download');
                        // 文書ファイルの更新
                        Route::patch('/{document_number}/file', [DocumentsFilesController::class, 'updateFile'])->name('updateFile');
                    }
                );
            }
        );
    }
);
