<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書ファイルのダウンロードの異常テスト
 */
class DocumentFileDownloadErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * 文書番号が存在しない
     */
    public function test_no_exist_document_number(): void
    {
        $root = sprintf("%s/%s/file", self::ROOT_DOCUMENT, 'aa5bffed-89eb-41ca-94f2-607e9d971503');
        $response = $this->getJson($root);
        $response->assertStatus(self::CODE_400);
    }

    /**
     * 文書番号の形式が異なる(uuid:ver4)
     * 文書番号のレコードが存在している
     */
    public function test_regex_document_number(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = sprintf("%s/%s/file", self::ROOT_DOCUMENT, 'aa5bf-edd89eb-41ca-94f2-607e9d971503');
        $response = $this->getJson($root);
        $response->assertStatus(self::CODE_400);

        $this->deleteDocumentAfterTest($document->document_number);
    }
}
