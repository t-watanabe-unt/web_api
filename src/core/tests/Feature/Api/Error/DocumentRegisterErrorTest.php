<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書登録時の異常テスト
 */
class DocumentRegisterErrorTest extends DocumentCommonFunctionsTest
{
    /**
     * テスト:リクエスト形式違い
     *
     * @group  document
     * @group  document_register_error
     * @return void
     */
    public function test_different_request_method(): void
    {
        $requestBody = [];
        $fileName = 'test.png';
        $file = UploadedFile::fake()->create($fileName, 2000, config('mimetype.file_extension.png'));
        $requestBody['file'] = $file;
        // 文書の登録(GETは、検索でバリデーションエラーとなるため400)
        $this->fail_anyJson($requestBody, 'getJson', self::RESPONSE_ERROR, self::CODE_400);
        $this->fail_anyJson($requestBody, 'patchJson', self::RESPONSE_ERROR, self::CODE_405);
        $this->fail_anyJson($requestBody, 'putJson', self::RESPONSE_ERROR, self::CODE_405);
        $this->fail_anyJson($requestBody, 'deleteJson', self::RESPONSE_ERROR, self::CODE_405);
    }

    /**
     * テスト:リクエストのバリューなし
     *
     * @group  document
     * @group  document_register_error
     * @return void
     */
    public function test_no_value(): void
    {
        $fileName = 'test.png';
        $file = UploadedFile::fake()->create($fileName, 2000, config('mimetype.file_extension.png'));

        // name属性:fileなし
        $this->no_name_file($file);
        // name属性:fileに未入力
        $this->no_value_file();
        // リクエストボディなし
        $this->custom_postJson([], self::RESPONSE_ERROR, self::CODE_400);
        // リクエストボディ(文書の属性:attributeが空)
        $this->no_value_attribute($file);
        // リクエストボディ(文書の属性:keyを空)
        $this->no_key_attribute($file);
    }

    /**
     * name属性:fileなし
     *
     * @param  \Illuminate\Http\Testing\File $file
     * @return void
     */
    private function no_name_file($file)
    {
        $requestBody['files'] = $file;
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }

    /**
     * name属性:fileに未入力
     *
     * @return void
     */
    private function no_value_file()
    {
        $requestBody['file'] = '';
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }

    /**
     * リクエストボディ(文書の属性:attributeが空)
     *
     * @param  \Illuminate\Http\Testing\File $file
     * @return void
     */
    private function no_value_attribute($file)
    {
        $requestBody['file'] = $file;
        $requestBody['attribute'] = [
            'title' => ''
        ];
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }

    /**
     * リクエストボディ(文書の属性:keyが空)
     *
     * @param  \Illuminate\Http\Testing\File $file
     * @return void
     */
    private function no_key_attribute($file)
    {
        $requestBody['file'] = $file;
        $requestBody['attribute'] = [
            '' => 'test'
        ];
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }

    /**
     * 入力文字数の最大桁オーバー
     *
     * @group  document
     * @group  document_register_error
     * @return void
     */
    public function test_over_number_of_characterers(): void
    {
        $fileName = 'test.png';
        $file = UploadedFile::fake()->create($fileName, 2000, config('mimetype.file_extension.png'));
        // 文書の属性:Keyの入力値オーバー
        $this->over_10_character_key($file);
        // 文書の属性:Valueの入力値オーバー
        $this->over_10_character_value($file);
    }

    /**
     * 文書の属性の`Key`の入力が10文字超え
     *
     * @param  \Illuminate\Http\Testing\File $file
     * @return void
     */
    private function over_10_character_key($file)
    {
        $requestBody['file'] = $file;
        $requestBody['attribute'] = [
            'testtitleda' => 'test',
        ];
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }

    /**
     * 文書の属性の`Value`の入力が20文字超え
     *
     * @param  \Illuminate\Http\Testing\File $file
     * @return void
     */
    private function over_10_character_value($file)
    {
        $requestBody['file'] = $file;
        $requestBody['attribute'] = [
            'title' => 'WEB_API仕様書ボリューム1.0.01',
        ];
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }

    /**
     * 文書の属性のKeyに英数字以外を入力
     *
     * @group  document
     * @group  document_register_error
     * @return void
     */
    public function test_regex_key(): void
    {
        $fileName = 'test.png';
        $file = UploadedFile::fake()->create($fileName, 2000, config('mimetype.file_extension.png'));
        $this->regex_key($file);
    }

    /**
     * 文書の属性:Keyに英数字以外でリクエスト
     *
     * @param  object $file
     * @return void
     */
    private function regex_key($file)
    {
        $requestBody['file'] = $file;
        $requestBody['attribute'] = [
            'aあa' => 'WEB_API仕様書ボリューム1.0.01',
        ];
        $this->custom_postJson($requestBody, self::RESPONSE_ERROR, self::CODE_400);
    }
}
