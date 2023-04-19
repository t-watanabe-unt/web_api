<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書ファイルの更新の異常テスト
 */
class DocumentFileUpdateErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * 登録されている拡張子とリクエストされた拡張子が異なる
     *
     * @group document_file
     * @group document_file_update_error
     */
    public function test_different_extension(): void
    {
        // テスト前の作成用文書情報
        $fileInfoForBeforeTest['extension'] = 'doc';
        $fileInfoForBeforeTest['mimetype'] = config('mimetype.file_extension_mime_type.pdf');

        // 更新用リクエストボディ情報
        $fileInfoForRequest['extension'] = 'pdf';
        $fileInfoForRequest['mimetype'] = config('mimetype.file_extension_mime_type.pdf');
        $fileInfoForRequest['filename'] = 'updateFile';
        $this->fileUpdate($fileInfoForBeforeTest, $fileInfoForRequest, self::CODE_400);
    }

    /**
     * リクエストボディなし
     *
     * @group  document_file
     * @group  document_file_update_error
     * @return void
     */
    public function test_no_file(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = sprintf('%s/%s/file', self::ROOT_DOCUMENT, $document->document_number);
        $response = $this->patchJson($root);
        $response->assertStatus(self::CODE_400);
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 存在しない文書番号
     *
     * @group  document_file
     * @group  document_file_update_error
     * @return void
     */
    public function test_no_exist_document_number(): void
    {
        $root = sprintf('%s/%s/file', self::ROOT_DOCUMENT, 'aa5bffed-89eb-41ca-94f2-607e9d971503');
        $response = $this->patchJson($root);
        $response->assertStatus(self::CODE_400);
    }

    /**
     * 文書番号の形式が異なる(uuid:ver4)
     * 文書番号のレコードが存在している
     *
     * @group document_file
     * @group document_file_update_error
     */
    public function test_regex_document_number(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = sprintf("%s/%s/file", self::ROOT_DOCUMENT, 'aa5bf-edd89eb-41ca-94f2-607e9d971503');
        $response = $this->patchJson($root);
        $response->assertStatus(self::CODE_400);
        $this->deleteDocumentAfterTest($document->document_number);
    }
}
