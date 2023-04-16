<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\DocumentFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * 共通関数定義クラス
 */
class DocumentCommonFunctionsTest extends TestCase
{
    /**
     * ルート: 文書の登録,文書の検索
     */
    const ROOT_DOCUMENT = 'api/v1/documents';

    /**
     * ステータスコード
     */
    const CODE_200 = 200;
    const CODE_204 = 204;
    const CODE_400 = 400;
    const CODE_404 = 404;
    const CODE_405 = 405;
    const CODE_500 = 500;

    /**
     * レスポンス形式: エラー時
     */
    const RESPONSE_ERROR = [
        'message'
    ];

    /**
     * レスポンス形式: 文書の属性なし
     */
    const RESPONSE_ARRAY_WITHOUT_ATTRIBUTE = [
        'document_number',
        'document_name',
        'document_mime_type',
    ];

    /**
     * レスポンス形式: 文書の属性あり
     */
    const RESPONSE_ARRAY_SEARCH = [
        '*' => [
            'document_number',
            'document_name',
            'document_mime_type',
        ]
    ];

    /**
     * テスト用指定外のMimeタイプ
     */
    const EXCEPT_SPECIFIED_MIME_TYPE = [
        'zip' => 'application/zip',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    ];

    /**
     * 文書の登録内容確認用の配列生成
     * 入力値がDocumentsに登録されたレコードとして登録されているか
     *
     * @param  object $file
     * @return array
     */
    public function setDocumentForRegistered($file)
    {
        $registeredItem = [];
        $documentExtension = $file->getClientOriginalExtension();
        $registeredItem['document_name'] = basename($file->getClientOriginalName(), '.' . $documentExtension);
        $registeredItem['document_mime_type'] = $file->getMimeType();
        $registeredItem['document_extension'] = $documentExtension;

        return $registeredItem;
    }

    /**
     * 文書の属性の登録内容確認用の配列生成
     * 入力値がattributesに登録されたレコードとして登録されているか
     *
     * @param  array $attributes
     * @return array
     */
    public function setAttributesForRegistered($attributes)
    {
        $registeredItem = [];
        foreach ($attributes as $key => $value) {
            $registeredItem = [
                'key' => $key,
                'value' => $value
            ];
        }
        return $registeredItem;
    }

    /**
     * ファイルと文書の属性のレスポンス(文書の登録時)
     *
     * @param  array $requestBody
     * @param  array $responseBody
     * @param  int   $statusCode
     * @return void
     */
    public function custom_postJson($requestBody, $responseBody, $statusCode)
    {
        $response = $this->postJson(self::ROOT_DOCUMENT, $requestBody);
        $response->assertJsonStructure($responseBody);
        $response->assertStatus($statusCode);

        // 正常に登録した場合
        if (self::CODE_200 === $statusCode) {
            // リクエスト後のレスポンスを取得(配列に戻す)
            $registerd = json_decode($response->getContent(), true);
            $document = Document::where('document_number', $registerd['document_number'])->first();
            $registeredDocument = $this->setDocumentForRegistered($requestBody['file']);

            // 文書の属性がある場合(存在チェック)
            if (isset($requestBody['attribute'])) {
                $registeredAttributes = $this->setAttributesForRegistered($requestBody['attribute']);
                $registeredAttributes['document_id'] = $document->id;
                $this->assertDatabaseHas('attributes', $registeredAttributes);
            }
            // 文書の登録(存在チェック)
            $this->assertDatabaseHas('documents', $registeredDocument);

            // テスト後にレコードと文書ファイルの削除
            $this->deleteDocumentAfterTest($registerd['document_number']);
        }
    }

    /**
     * リクエストメソッド違いのチェック
     *
     * @param  array  $requestBody
     * @param  string $method
     * @param  array  $responseBody
     * @return void
     */
    public function fail_anyJson($requestBody, $method, $responseBody, $statusCode)
    {
        $response = $this->$method(self::ROOT_DOCUMENT, $requestBody);
        $response->assertJsonStructure($responseBody);
        $response->assertStatus($statusCode);
    }

    /**
     * レスポンスボディの生成
     *
     * @param  string $fileExtension
     * @param  string $mimeType
     * @param  array  $attributes
     * @return array
     */
    public function getRequestBodyForDocument($fileExtension, $mimeType, $attributes = null)
    {
        $requestBody = [];
        $fileName = 'test.' . $fileExtension;
        $file = UploadedFile::fake()->create($fileName, 2000, $mimeType);
        $requestBody['file'] = $file;

        if (!is_null($attributes)) {
            $requestBody['attribute'] = $attributes;
        }

        return $requestBody;
    }

    /**
     * テスト実行: 単一ファイルでのチェック
     *
     * @param  array $mimeTypes
     * @param  array $attributes
     * @return void
     */
    public function run_one_extension($attributes = null)
    {
        $requestBody = $this->getRequestBodyForDocument('png', 'application/pdf', $attributes);
        // 文書の属性なし
        if (empty($attributes)) {
            $this->custom_postJson($requestBody, self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, self::CODE_200);
        } else {
            $responseBody = array_merge(self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, array_keys($attributes));
            $this->custom_postJson($requestBody, $responseBody, self::CODE_200);
        }
    }

    /**
     * テスト実行:全ファイルの拡張子でのチェック
     *
     * @param  array $mimeTypes
     * @param  array $attributes
     * @return void
     */
    public function run_all_extension($mimeTypes, $attributes = null)
    {
        foreach ($mimeTypes as $key => $value) {
            $requestBody = $this->getRequestBodyForDocument($key, $value, $attributes);
            // 文書の属性なし
            if (empty($attributes)) {
                $this->custom_postJson($requestBody, self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, self::CODE_200);
            } else {
                $responseBody = array_merge(self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, array_keys($attributes));
                $this->custom_postJson($requestBody, $responseBody, self::CODE_200);
            }
        }
    }

    /**
     * 文書登録テスト後に、登録したレコードとファイルを削除する
     *
     * @param  string $document_number
     * @return void
     */
    public function deleteDocumentAfterTest($document_number)
    {
        $document = Document::where('documents.document_number', '=', $document_number)->with('attributes')->first();
        $document->delete();
        DocumentFile::fromDocument($document)->delete();
    }

    /**
     * テスト前に文書を登録する
     *
     * @param  array $requestBody
     * @param  array $attributes
     * @return object
     */
    public function registerDocumentBeforeTest($attributes = null)
    {
        $requestBody = $this->getRequestBodyForDocument('pdf', 'application/pdf', $attributes);
        $response = $this->postJson(self::ROOT_DOCUMENT, $requestBody);
        $response->assertStatus(self::CODE_200);
        $registerd = json_decode($response->getContent(), true);

        // 文書の属性なし
        if (empty($attributes)) {
            $document = Document::where('document_number', $registerd['document_number'])->first();
        } else {
            $document = Document::where('document_number', $registerd['document_number'])->with('attributes')->first();
        }
        return $document;
    }

    /**
     * 検索用クエリの生成
     *
     * @param  array  $searchParameter
     * @param  string $operator
     * @return string
     */
    public function getQueryParametes($searchParameter)
    {
        if (empty($searchParameter)) {
            return '';
        }

        $query = "?";
        $count = 0;
        foreach ($searchParameter as $key => $values) {
            foreach ($values as $operator => $value) {
                if ($count > 0) {
                    $query .= "&";
                }
                $encodedOperator = urlencode($operator);
                $query .= sprintf("%s[%s]=%s", $key, $encodedOperator, $value);
                $count++;
            }
        }
        return $query;
    }

    /**
     * 単一のクエリで検索
     * 終了後に削除
     *
     * @param  array  $searchParameters
     * @param  array  $responseBody
     * @param  object $document
     * @param  int    $statusCode
     * @return void
     */
    public function searchWithOneQuery($searchParameter, $responseBody, $document, $statusCode)
    {
        $query = $this->getQueryParametes($searchParameter);
        $response = $this->getJson(self::ROOT_DOCUMENT . $query);
        $response->assertJsonStructure($responseBody);
        $response->assertStatus($statusCode);
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 複数のクエリで検索
     * 終了後削除
     *
     * @param  array  $searchParameters
     * @param  array  $responseBody
     * @param  object $documents
     * @param  int    $statusCode
     * @return void
     */
    public function searchWithSomeQuery($searchParameters, $responseBody, $documents, $statusCode)
    {
        $query = $this->getQueryParametes($searchParameters);
        $response = $this->getJson(self::ROOT_DOCUMENT . $query);
        $response->assertJsonStructure($responseBody);
        $response->assertStatus($statusCode);

        // 複数ドキュメント削除
        foreach ($documents as $document) {
            $this->deleteDocumentAfterTest($document->document_number);
        }
    }
}
