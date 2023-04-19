<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書の検索正常テスト
 */
class DocumentSearchPassTest extends DocumentCommonFunctionsTest
{
    /**
     * 検索結果なし(データなし)
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_no_data(): void
    {
        // 登録なしで検索
        $encodedOperator = urlencode('=');
        $parameter = "?title[$encodedOperator]=API";
        $response = $this->getJson(self::ROOT_DOCUMENT . $parameter);
        $response->assertJsonStructure(self::RESPONSE_ERROR);
        $response->assertStatus(self::CODE_200);
    }

    /**
     * 単一の文書のレスポンス形式の確認
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_get_document(): void
    {
        $attribute['title'] = 'OpenAPI'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ARRAY_SEARCH, $document, self::CODE_200);
    }

    /**
     * 単一の文書のレスポンス形式の確認(>=での確認)
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_get_document_case2(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['date'] = ['>=' => '2022-01-02'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ARRAY_SEARCH, $document, self::CODE_200);
    }

    /**
     * 単一の文書のレスポンス形式の確認(<=での確認)
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_get_document_case3(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['date'] = ['<' => '2023-04-01'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ARRAY_SEARCH, $document, self::CODE_200);
    }

    /**
     * 単一の文書のレスポンス形式の確認(<=での確認)
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_get_document_case4(): void
    {
        $attribute['date'] = '2022-12-03'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['date'] = ['<=' => '2023-04-01'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ARRAY_SEARCH, $document, self::CODE_200);
    }

    /**
     * 複数の文書のレスポンス形式の確認
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_get_documents(): void
    {
        // 2種登録
        $attributes = [
            0 => ['title' => 'WebApiアプリケーション概要'],
            1 => ['date' => '2023-02-01'],
        ];
        foreach ($attributes as $attribute) {
            $documents[] = $this->registerDocumentBeforeTest($attribute);
        }
        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameters['title'] = ['=' => 'api'];
        $searchParameters['date'] = ['>' => '2023-01-01'];
        $this->searchWithSomeQuery($searchParameters, self::RESPONSE_ARRAY_SEARCH, $documents, self::CODE_200);
    }

    /**
     * 文書の属性Keyを10文字で検索
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_query_max_key(): void
    {
        $attribute['titletitle'] ='sample_api_document'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['titletitle'] = ['=' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ARRAY_SEARCH, $document, self::CODE_200);
    }

    /**
     * 文書の属性Valueを20文字で検索
     *
     * @group  document
     * @group  document_search_pass
     * @return void
     */
    public function test_query_max_value(): void
    {
        $attribute['title'] = 'sample_api_documents'; // 登録用
        $document = $this->registerDocumentBeforeTest($attribute);

        // 検索用パラメータをセット(key,比較演算子,value)
        $searchParameter['title'] = ['=' => 'api'];
        $this->searchWithOneQuery($searchParameter, self::RESPONSE_ARRAY_SEARCH, $document, self::CODE_200);
    }
}
