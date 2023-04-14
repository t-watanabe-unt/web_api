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
    // 検索結果あり
    // 検索結果あり(複数レスポンス)
    // 複数項目での検索

    /**
     * 検索用クエリの生成
     *
     * @param array $searchParameter
     * @param string $operator
     * @return void
     */
    private function getQueryParametes($searchParameter, $operator)
    {
        $query = "?";
        $encodedOperator = urlencode($operator);
        foreach ($searchParameter as $key => $value) {
            if ($key > 0) {
                $query .= "&";
            }
            $query .= sprintf("%s[%s]=%s", $key, $encodedOperator, $value);
        }
        return $query;
    }

    /**
     * 単一のクエリでのチェック
     * 終了後に削除
     *
     * @param object $document
     * @param string $operator
     * @return void
     */
    public function checkEqualWithOneQuery($searchParameter, $operator, $document)
    {
        $query = $this->getQueryParametes($searchParameter, $operator);
        $response = $this->getJson(self::ROOT_DOCUMENT . $query);
        $response->assertJsonStructure(self::RESPONSE_ARRAY_SEARCH);
        $response->assertStatus(self::CODE_200);
        $this->deleteDocumentAfterTest($document->document_number);
    }

    public function checkEqualWithSomeQuery($searchParameter, $operators, $documents)
    {
        // foreach ()
        // クエリを結合し、複数条件で検索
        $query = $this->getQueryParametes($searchParameter, $operator);
        $response = $this->getJson(self::ROOT_DOCUMENT . $query);
        $response->assertJsonStructure(self::RESPONSE_ARRAY_SEARCH);
        $response->assertStatus(self::CODE_200);

        // 複数ドキュメント削除
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 検索結果なし(データなし)
     *
     * @return void
     */
    public function test_no_data(): void
    {
        // 登録なしで検索
        $encodedOperator = urlencode('=');
        $parameter = "?title[$encodedOperator]='API'";
        $response = $this->getJson(self::ROOT_DOCUMENT . $parameter);
        $response->assertJsonStructure(self::RESPONSE_ERROR);
        $response->assertStatus(self::CODE_200);
    }

    /**
     * 単一の文書のレスポンス形式の確認
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @return void
     */
    public function test_get_document(): void
    {
        $attribute['title'] = 'OpenAPI'; // 登録用
        $searchParameter['title'] = 'api'; // 検索用

        $document = $this->registerDocumentBeforeTest($attribute);
        $operator = '=';
        $this->checkEqualWithOneQuery($searchParameter, $operator, $document);
    }

    /**
     * 複数の文書のレスポンス形式の確認
     * 検索用でデータ登録後、そのデータが取得できるかをチェック
     *
     * @return void
     */
    public function test_get_documents(): void
    {
        // 2種登録
        $attributes = [
            0 => ['title' => 'WebApiアプリケーション概要'],
            1 => ['date' => '2023-02-01'],
        ];
        $operators = [
            '=',
            '>'
        ];
        foreach ($attributes as $key => $attribute) {
            $documents[] = $this->registerDocumentBeforeTest($attribute);
        }
        $searchParameters = [
            'api',
            '2023-01-01'
        ];
        $this->checkEqualWithSomeQuery($searchParameters, $operators, $documents);
    }

    /**
     * 文書の属性Keyを10文字で検索
     *
     * @return void
     */
    public function test_query_max_key(): void
    {
        $attribute['titletitle'] ='sample_api_document'; // 登録用
        $searchParameter['titletitle'] = 'api'; // 検索用
        $document = $this->registerDocumentBeforeTest($attribute);
        $operator = '=';
        $this->checkEqualWithOneQuery($searchParameter, $operator, $document);
    }

    /**
     * 文書の属性Valueを20文字で検索
     *
     * @return void
     */
    public function test_query_max_value(): void
    {
        $attribute['title'] = 'sample_api_documents'; // 登録用
        $searchParameter['title'] = 'api'; // 検索用
        $document = $this->registerDocumentBeforeTest($attribute);
        $operator = '=';
        $this->checkEqualWithOneQuery($searchParameter, $operator, $document);
    }
}
