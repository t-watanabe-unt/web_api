<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の削除の異常テスト
 */
class DocumentDeleteErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * 文書番号が存在しない
     */
    public function test_no_exist_document_number(): void
    {
        $root = self::ROOT_DOCUMENT . '/' . 'aa5bffed-89eb-41ca-94f2-607e9d971503';
        $response = $this->deleteJson($root);
        $response->assertStatus(self::CODE_400);
    }

    /**
     * 文書番号の形式が異なる(uuid:ver4)
     * 文書番号のレコードが存在している
     */
    public function test_regex_document_number(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = self::ROOT_DOCUMENT . '/' . 'aa5bf-edd89eb-41ca-94f2-607e9d971503';
        $response = $this->deleteJson($root);
        $response->assertStatus(self::CODE_400);

        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 文書番号なし
     */
    public function test_no_document_number(): void
    {
        $root = self::ROOT_DOCUMENT;
        $response = $this->deleteJson($root);
        $response->assertStatus(self::CODE_405);
    }
}
