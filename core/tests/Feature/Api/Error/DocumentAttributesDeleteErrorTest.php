<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の属性の削除 異常テスト
 */
class DocumentAttributesDeleteErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * ルーティングにKeyなし
     *
     * @group  document_attributes
     * @group  document_attributes_delete_error
     * @return void
     */
    public function test_no_key_in_path(): void
    {
        // keyが削除対象の文書の属性Key
        $key = 'title';
        $attributes[$key] = 'API仕様書';
        $this->deleteAttribute([], '', self::CODE_404);
    }

    /**
     * 存在しない文書番号
     *
     * @group  document_attributes
     * @group  document_attributes_delete_error
     * @return void
     */
    public function test_no_exist_document_number(): void
    {
        $root = sprintf('%s/%s/attributes/%s', self::ROOT_DOCUMENT, 'aa5bffed-89eb-41ca-94f2-607e9d971503', 'title');
        $response = $this->deleteJson($root);
        $response->assertStatus(self::CODE_400);
    }

    /**
     * 登録した文書の属性key以外でリクエスト
     *
     * @group  document_attributes
     * @group  document_attributes_delete_error
     * @return void
     */
    public function test_no_exist_key_in_path(): void
    {
        $key = 'title';
        $attributes[$key] = 'API仕様書';
        $this->deleteAttribute($attributes, 'test', self::CODE_400);
    }

    /**
     * 文書番号の形式が異なる(uuid:ver4)
     * 文書番号のレコードが存在している
     *
     * @group document_attributes
     * @group document_attributes_delete_error
     */
    public function test_regex_document_number(): void
    {
        $document = $this->registerDocumentBeforeTest();
        $root = sprintf('%s/%s/attributes/%s', self::ROOT_DOCUMENT, 'aa5bffed-89eb-41ca-94f2-607e9d971503', 'title');
        $response = $this->deleteJson($root);
        $response->assertStatus(self::CODE_400);
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のKeyを11文字で入力
     *
     * @group document_attributes
     * @group document_attributes_delete_error
     */
    public function test_over_10_character_key(): void
    {
        // 追加の文書の属性をセット
        $key = 'titletitlet';
        $this->deleteAttribute([], $key, self::CODE_400);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のKeyを英数字以外で入力
     *
     * @group document_attributes
     * @group document_attributes_delete_error
     */
    public function test_regex_key(): void
    {
        // 追加の文書の属性をセット
        $key = 'titleあet';
        $this->deleteAttribute([], $key, self::CODE_400);
    }
}
