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
     * リクエストボディの生成
     * 指定のリクエストボディに整形
     *
     * @param  string $fileExtension
     * @param  string $mimeType
     * @param  array  $attributes
     * @return array
     */
    public function getRequestBodyForDocument($fileExtension, $mimeType, $attributes = null, $specifiedfileName = null, $specifiedSize = null)
    {
        $requestBody = [];
        $fileName = sprintf("test.%s", $fileExtension);
        $fileSize = 2000;

        // ファイル名指定
        if (!empty($specifiedfileName)) {
            $fileName = sprintf("%s.%s", $specifiedfileName, $fileExtension);
        }

        // ファイルサイズ指定あり
        if (!empty($specifiedSize)) {
            $fileSize = $specifiedSize;
        }

        $file = UploadedFile::fake()->create($fileName, $fileSize, $mimeType);
        $requestBody['file'] = $file;

        // 文書の属性の登録有り
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
     * 登録したいattributesとmimeTypeを指定
     *
     * @param  array $requestBody
     * @param  array $attributes
     * @return object
     */
    public function registerDocumentBeforeTest($attributes = null, $mimeType = null)
    {
        $requestBody = $this->getRequestBodyForDocument('pdf', 'application/pdf', $attributes);

        if (!empty($mimeType)) {
            $requestBody = $this->getRequestBodyForDocument($mimeType['extension'], $mimeType['mimetype'], $attributes);
        }

        $response = $this->postJson(self::ROOT_DOCUMENT, $requestBody);
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

    /**
     * 指定した拡張子とMimeタイプによるテスト
     *
     * @param  array $ExtensionWithMimeType
     * @return void
     */
    public function downloadWithAllMimetypes($ExtensionWithMimeType, $statusCode)
    {
        foreach ($ExtensionWithMimeType as $extension => $mimetype) {
            $mimeType['extension'] = $extension;
            $mimeType['mimetype'] = $mimetype;
        }
        $document = $this->registerDocumentBeforeTest([], $mimeType);
        $root = sprintf('%s/%s/file', self::ROOT_DOCUMENT, $document->document_number);
        $response = $this->getJson($root);
        $response->assertDownload();
        $response->assertHeader('content-type', $mimetype);
        $response->assertStatus($statusCode);
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 文書ファイルの更新テスト
     *
     * @param  int $statusCode
     * @return void
     */
    public function fileUpdate($fileInfoForBeforeTest, $fileInfoForRequest, $statusCode)
    {
        $document = $this->registerDocumentBeforeTest([], $fileInfoForBeforeTest);
        $requestBody = $this->getRequestBodyForDocument($fileInfoForRequest['extension'], $fileInfoForRequest['mimetype'], [], $fileInfoForRequest['filename']);
        $root = sprintf('%s/%s/file', self::ROOT_DOCUMENT, $document->document_number);
        $response = $this->patchJson($root, $requestBody);
        $response->assertStatus($statusCode);

        // 正常テストで、更新後の登録内容をチェック
        if (self::CODE_200 === $statusCode) {
            $registeredDocument = $this->setDocumentForRegistered($requestBody['file']);
            $this->assertDatabaseHas('documents', $registeredDocument);
        }
        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 文書の属性の更新
     *
     * @param  array  $attributeBeforeTest
     * @param  string $key
     * @param  string $updateValue
     * @param  int    $statusCode
     * @return void
     */
    public function updateAttribute($attributeBeforeTest, $key, $updateValue, $statusCode)
    {
        $document = $this->registerDocumentBeforeTest($attributeBeforeTest);

        // 変更する内容
        $requestBody['value'] = $updateValue;
        $attributesRegistered[$key] = $updateValue;
        $root = sprintf("%s/%s/attributes/%s", self::ROOT_DOCUMENT, $document->document_number, $key);
        $response = $this->patchJson($root, $requestBody);

        // attributesでの更新内容をチェック
        $registeredAttributes = $this->setAttributesForRegistered($attributesRegistered);
        $registeredAttributes['document_id'] = $document->id;
        $response->assertStatus($statusCode);

        if (self::CODE_200 === $statusCode) {
            $this->assertDatabaseHas('attributes', $registeredAttributes);
        }

        $this->deleteDocumentAfterTest($document->document_number);
    }

    /**
     * 登録されている文書の属性を削除
     *
     * @param  array  $attributes
     * @param  string $key
     * @param  int    $statusCode
     * @return void
     */
    public function deleteAttribute($attributes, $key, $statusCode)
    {
        $document = $this->registerDocumentBeforeTest($attributes);
        $root = sprintf("%s/%s/attributes/%s", self::ROOT_DOCUMENT, $document->document_number, $key);
        $response = $this->deleteJson($root);
        $response->assertStatus($statusCode);

        // attributesでの更新内容をチェック
        $registeredAttributes['key'] = $key;
        $registeredAttributes['document_id'] = $document->id;
        $response->assertStatus($statusCode);

        if (self::CODE_204 === $statusCode) {
            $this->assertDatabaseMissing('attributes', $registeredAttributes);
        }

        $this->deleteDocumentAfterTest($document->document_number);
    }
}
