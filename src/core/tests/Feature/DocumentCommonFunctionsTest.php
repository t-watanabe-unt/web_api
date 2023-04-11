<?php

namespace Tests\Feature;

use App\Constants\MimeTypesConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DocumentCommonFunctionsTest extends TestCase
{
    /**
     * ルート: 文書の登録,文書の検索
     */
    const ROOT_REGISTER = 'api/v1/documents';

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
    const RESPONSE_ARRAY_WITH_ATTRIBUTES = [
        'document_number',
        'document_name',
        'document_mime_type',
        '*',
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
     * ファイルと文書の属性のレスポンス
     *
     * @param string $fileName
     * @param string $mimeType
     * @return void
     */
    public function custom_postJson($requestBody, $responseBody, $statusCode)
    {
        $response = $this->postJson(self::ROOT_REGISTER, $requestBody);
        $response->assertJsonStructure($responseBody);
        $response->assertStatus($statusCode);
    }

    /**
     * リクエストメソッド違いのチェック:文書の登録(ファイルと文書の属性)
     *
     * @param array $requestBody
     * @param string $method
     * @param array $responseBody
     * @return void
     */
    public function fail_anyJson($requestBody, $method, $responseBody, $statusCode)
    {
        $response = $this->$method(self::ROOT_REGISTER, $requestBody);
        $response->assertJsonStructure($responseBody);
        $response->assertStatus($statusCode);
    }

    /**
     * レスポンスボディの生成
     *
     * @param string $fileExtension
     * @param string $mimeType
     * @param array $attributes
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
     * テスト実行:文書登録
     *
     * @param array $mimeTypes
     * @param array $attributes
     * @return void
     */
    public function run_testing($mimeTypes, $attributes = null)
    {
        foreach ($mimeTypes as $key => $value) {
            $requestBody = $this->getRequestBodyForDocument($key, $value, $attributes);

            // 文書の属性有り
            if (!is_null($attributes)) {
                $responseBody = array_merge(self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, array_keys($attributes));
                $this->custom_postJson($requestBody, $responseBody, self::CODE_200);
            } else {
                $this->custom_postJson($requestBody, self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, self::CODE_200);
            }
        }
    }
}
