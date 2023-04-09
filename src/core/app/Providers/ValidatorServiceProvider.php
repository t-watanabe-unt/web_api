<?php

namespace App\Providers;

use App\Models\Attribute;
use App\Models\Document;
use Illuminate\Support\ServiceProvider;
use Validator;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // 文書の属性のkeyのチェック(文書登録時)
        Validator::extend(
            'attribute_name_register', function ($attribute, $value, $parameters, $validator) {
                return Attribute::isValidAttributeNameWithRegister($attribute, $value);
            }
        );

        // 文書の属性のkeyのチェック(登録時以外)
        Validator::extend(
            'attribute_name', function ($attribute, $value, $parameters, $validator) {
                return Attribute::isValidAttributeName($attribute, $value);
            }
        );

        // 文書の属性のkeyのチェック(ルートパラメータ内)
        Validator::extend(
            'attribute_name_route', function ($attribute, $value, $parameters, $validator) {
                return Attribute::isValidAttributeNameWithRoute($attribute, $value);
            }
        );

        // 文書番号と文書のkeyが一致するレコードの存在チェック
        Validator::extend(
            'exists_attribute_key', function ($attribute, $value, $parameters, $validator) {
                return Attribute::isValidExistAttributeKey($attribute, $value);
            }
        );

        // 文書検索時の比較演算子のチェック
        Validator::extend(
            'operator', function ($attribute, $value, $parameters, $validator) {
                return Attribute::isValidOperatorValue($attribute, $value);
            }
        );

        // 文書検索時の比較演算子のチェック
        Validator::extend(
            'file_extension', function ($attribute, $value, $parameters, $validator) {
                return Document::isValidFileExtension($attribute, $value);
            }
        );
    }
}
