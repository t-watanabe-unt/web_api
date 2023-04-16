<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の削除の正常テスト
 */
class DocumentDeletePassTest extends DocumentCommonFunctionsTest
{
    /**
     * 文書の削除
     *
     * @return void
     */
    public function test_delete_document(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = self::ROOT_DOCUMENT . '/' . $document->document_number;
        $response = $this->deleteJson($root);
        $response->assertStatus(self::CODE_204);
    }
}
