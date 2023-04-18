<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の属性 更新の正常テスト
 */
class DocumentAttributesUpdatePassTest extends DocumentCommonFunctionsTest
{
    /**
     * 文書の属性の更新
     * 対象の文書に存在する属性Keyに対して更新
     */
    public function test_update_attribute(): void
    {
        // 変更対象の文書の属性をセット
        $key = 'title';
        $attributes[$key] = 'API仕様書';
        $updateValue = 'API共通仕様書';

        $this->updateAttribute($attributes, $key, $updateValue, self::CODE_200);
    }

    /**
     * 文書の属性の登録
     * 対象の文書に存在する属性Keyに対して更新
     */
    public function test_register_new_attribute(): void
    {
        // 追加の文書の属性をセット
        $key = 'title';
        $updateValue = 'API共通仕様書';

        $this->updateAttribute([], $key, $updateValue, self::CODE_200);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のKeyを10文字で入力
     */
    public function test_register_new_attribute_10_character_key(): void
    {
        // 追加の文書の属性をセット
        $key = 'titletitle';
        $updateValue = 'API共通仕様書';

        $this->updateAttribute([], $key, $updateValue, self::CODE_200);
    }

    /**
     * 文書の属性の登録
     * 文書の属性のValueを20文字で入力
     */
    public function test_register_new_attribute_20_character_value(): void
    {
        // 追加の文書の属性をセット
        $key = 'title';
        $updateValue = 'API共通仕様書_23_10_10_v1';

        $this->updateAttribute([], $key, $updateValue, self::CODE_200);
    }
}
