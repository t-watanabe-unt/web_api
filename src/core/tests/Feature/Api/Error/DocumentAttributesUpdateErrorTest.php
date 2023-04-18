<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の属性 更新の異常テスト
 */
class DocumentAttributesUpdateErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * ルーティングにKeyなし
     *
     * @return void
     */
    public function test_no_key_in_path(): void
    {
        $this->updateAttribute([], '', [], self::CODE_404);
    }

    /**
     * 存在しない文書番号
     *
     * @return void
     */
    public function test_no_exist_document_number(): void
    {
        $root = sprintf('%s/%s/attributes/%s', self::ROOT_DOCUMENT, 'aa5bffed-89eb-41ca-94f2-607e9d971503', 'title');
        $response = $this->patchJson($root);
        $response->assertStatus(self::CODE_400);
    }

    /**
     * 文書番号の形式が異なる(uuid:ver4)
     * 文書番号のレコードが存在している
     */
    public function test_regex_document_number(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = sprintf('%s/%s/attributes/%s', self::ROOT_DOCUMENT, 'aa5bffed-89eb-41ca-94f2-607e9d971503', 'title');
        $response = $this->patchJson($root);
        $response->assertStatus(self::CODE_400);
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のKeyを11文字で入力
     */
    public function test_over_10_character_key(): void
    {
        // 追加の文書の属性をセット
        $key = 'titletitlet';
        $updateValue = 'API共通仕様書';

        $this->updateAttribute([], $key, $updateValue, self::CODE_400);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のKeyを11文字で入力
     */
    public function test_regex_key(): void
    {
        // 追加の文書の属性をセット
        $key = 'titleあet';
        $updateValue = 'API共通仕様書';

        $this->updateAttribute([], $key, $updateValue, self::CODE_400);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のValueを21文字で入力
     */
    public function test_over_20_character_value(): void
    {
        // 追加の文書の属性をセット
        $key = 'title';
        $updateValue = 'API_共通仕様書_23_10_10_v1';

        $this->updateAttribute([], $key, $updateValue, self::CODE_400);
    }
}
