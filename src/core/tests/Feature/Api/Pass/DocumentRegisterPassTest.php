<?php

namespace Tests\Feature\Api\Pass;

use App\Constants\MimeTypesConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\Feature\DocumentCommonFunctionsTest;
use Tests\TestCase;

class DocumentRegisterPassTest extends DocumentCommonFunctionsTest
{
    /**
     * テスト:ファイル単体のみのリクエスト
     *
     * @return void
     */
    public function test_file_only(): void
    {
        $this->run_testing(MimeTypesConstant::MIME_EXTENSION, []);
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
        $this->run_testing(MimeTypesConstant::MIME_EXTENSION, $attributes);
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
        $this->run_testing(MimeTypesConstant::MIME_EXTENSION, $attributes);
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
        $this->run_testing(MimeTypesConstant::MIME_EXTENSION, $attributes);
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
        $this->run_testing(MimeTypesConstant::MIME_EXTENSION, $attributes);
    }
}
