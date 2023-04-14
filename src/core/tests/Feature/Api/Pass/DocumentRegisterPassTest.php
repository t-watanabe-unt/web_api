<?php

namespace Tests\Feature\Api\Pass;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
     * @return void
     */
    public function test_file_only(): void
    {
        $this->run_one_extension();
    }

    /**
     * テスト:対象のファイル拡張子全てのリクエスト
     *
     * @return void
     */
    public function test_all_mimetype(): void
    {
        $this->run_all_extension(config('mimetype.file_extension_mime_type'));
    }

    /**
     * テスト:ファイルと文書の属性(単体)のリクエスト
     *
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
}
