<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書ファイルの更新の正常テスト
 */
class DocumentFileUpdatePassTest extends DocumentCommonFunctionsTest
{
    /**
     * 文書のファイルの更新(同一拡張子とMimeタイプでの更新)
     * 文書を登録しその文書に対して更新処理を行う
     */
    public function test_document_file_update(): void
    {
        foreach (config('mimetype.file_extension_mime_type') as $extension => $mimeType) {
            // テスト前の作成用文書情報
            $fileInfoForBeforeTest['extension'] = $extension;
            $fileInfoForBeforeTest['mimetype'] = $mimeType;

            // 更新用リクエストボディ情報
            $fileInfoForRequest['extension'] = $extension;
            $fileInfoForRequest['mimetype'] = $mimeType;
            $fileInfoForRequest['filename'] = 'updateFile';
            $this->fileUpdate($fileInfoForBeforeTest, $fileInfoForRequest, self::CODE_200);
        }
    }
}
