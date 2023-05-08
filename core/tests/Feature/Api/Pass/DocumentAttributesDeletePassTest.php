<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の属性の削除 正常テスト
 */
class DocumentAttributesDeletePassTest extends DocumentCommonFunctionsTest
{
    /**
     * 文書の属性の削除
     * 対象の文書に存在する属性Keyに対して更新(属性が1つのみ登録)
     *
     * @group document_attributes
     * @group document_attributes_delete_pass
     */
    public function test_delete_from_an_attribute(): void
    {
        // keyが削除対象の文書の属性Key
        $key = 'title';
        $attributes[$key] = 'API仕様書';
        $this->deleteAttribute($attributes, $key, self::CODE_204);
    }

    /**
     * 文書の属性の削除
     * 対象の文書に存在する属性Keyに対して更新(属性が複数登録されてうちから1つを削除)
     *
     * @group document_attributes
     * @group document_attributes_delete_pass
     */
    public function test_delete_from_some_attribute(): void
    {
        // keyが削除対象の文書の属性Key
        $key = 'title';
        $attributes[$key] = 'API仕様書';
        $attributes['date'] = '2023-03-03';
        $this->deleteAttribute($attributes, $key, self::CODE_204);
    }
}
