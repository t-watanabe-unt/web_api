<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書のファイルをダウンロードする正常テスト
 */
class DocumentFileDownloadPassTest extends DocumentCommonFunctionsTest
{
    /**
     * 許可しているファイルの拡張子とMimeタイプのテスト
     * 文書を登録後に、その登録したファイルをDLする
     *
     * @group  document_file
     * @group  document_file_download_pass
     * @return void
     */
    public function test_download_with_permission(): void
    {
        $this->downloadWithAllMimetypes(config('mimetype.file_extension_mime_type'), self::CODE_200);
    }
}
