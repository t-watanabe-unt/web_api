<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の検索異常テスト
 */
class DocumentSearchErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * クエリなし(データは存在する状態)
     *
     * @return void
     */
    public function test_no_query(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter = [];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性Keyが空(データは存在する状態)
     *
     * @return void
     */
    public function test_no_key(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter[''] = ['=' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性Operatorが空(データは存在する状態)
     *
     * @return void
     */
    public function test_no_operator(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性Valueが空(データは存在する状態)
     *
     * @return void
     */
    public function test_no_value(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=' => ''];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性入力で複数入力
     * 片方は、正常だが片方は異常な入力(データは存在する状態)
     *
     * @return void
     */
    public function test_comvine_invalid_query(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=' => ''];
        $searchParameter['date'] = ['>' => '2023-03-01'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性Keyが11文字以上(データは存在する状態)
     *
     * @return void
     */
    public function test_10_character_key(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['titletitle1'] = ['=' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性Valueが21文字以上(データは存在する状態)
     *
     * @return void
     */
    public function test_20_character_value(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=' => 'WEB_API仕様書ボリューム1.0.01'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * クエリの文書の属性Keyが英数字以外(データは存在する状態)
     *
     * @return void
     */
    public function test_regex_key(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['aあa'] = ['=' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * 比較演算子が指定値以外(データは存在する状態)
     *
     * @return void
     */
    public function test_regex_operator_case1(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['>`' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * 比較演算子が指定値以外(データは存在する状態)
     *
     * @return void
     */
    public function test_regex_operator_case2(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['>`;' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * 比較演算子が指定値以外(データは存在する状態)
     *
     * @return void
     */
    public function test_regex_operator_case3(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=`;' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * 比較演算子が指定値以外(データは存在する状態)
     *
     * @return void
     */
    public function test_regex_operator_case4(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['  ' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }

    /**
     * 比較演算子が指定値以外(データは存在する状態)
     *
     * @return void
     */
    public function test_regex_operator_case5(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=`@ ' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ERROR, $document, self::CODE_400);
    }
}
