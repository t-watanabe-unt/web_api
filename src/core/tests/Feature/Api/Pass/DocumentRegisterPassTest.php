<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

/**
 * 文書登録の正常テスト
 */
class DocumentRegisterPassTest extends DocumentCommonFunctionsTest
{
    /**
     * テスト:ファイル単体のみのリクエスト
     *
     * @group  document
     * @group  document_register_pass
     * @return void
     */
    public function test_file_only(): void
    {
        $this->run_one_extension();
    }

    /**
     * テスト:対象のファイル拡張子全てのリクエスト
     *
     * @group  document
     * @group  document_register_pass
     * @return void
     */
    public function test_all_mimetype(): void
    {
        $this->run_all_extension(config('mimetype.file_extension_mime_type'));
    }

    /**
     * テスト:ファイルと文書の属性(単体)のリクエスト
     *
     * @group  document
     * @group  document_register_pass
     * @return void
     */
    public function test_file_with_attribute(): void
    {
        $attributes = [
            'title' => 'APIドキュメント',
        ];
        $this->run_one_extension($attributes);
    }

    /**
     * 文書の属性のKey最大値
     *
     * @group  document
     * @group  document_register_pass
     * @return void
     */
    public function test_file_with_attribute_max_key(): void
    {
        $attributes = [
            'titletitle' => 'APIドキュメント',
        ];
        $this->run_one_extension($attributes);
    }

    /**
     * 文書の属性のValue最大値
     *
     * @group  document
     * @group  document_register_pass
     * @return void
     */
    public function test_file_with_attribute_max_value(): void
    {
        $attributes = [
            'title' => 'APIドキュメント_2023.03.03',
        ];
        $this->run_one_extension($attributes);
    }

    /**
     * テスト:ファイルと文書の属性(複数)のリクエスト
     *
     * @group  document
     * @group  document_register_pass
     * @return void
     */
    public function test_file_with_attributes(): void
    {
        $attributes = [
            'title' => 'API参考書',
            'date' => '2023-01-01'
        ];
        $this->run_one_extension($attributes);
    }

    /**
     * ファイルサイズが100MBちょうどの時
     *
     * @group document
     * @group document_register_pass
     * @return void
     */
    public function test_just_size_100mb(): void
    {
        $requestBody = [];
        $fileName = 'test.png';

        // ファイルサイズを指定(101MB)
        $file = UploadedFile::fake()->create($fileName, 100000, config('mimetype.file_extension.png'));
        $requestBody['file'] = $file;
        $this->custom_postJson($requestBody, self::RESPONSE_ARRAY_WITHOUT_ATTRIBUTE, self::CODE_200);
    }
}
