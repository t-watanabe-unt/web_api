<?php

namespace Tests\Feature\Api\Error;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * 文書の検索異常テスト
 */
class DocumentSearchErrorTest extends TestCase
{
    // クエリなし
    // 値なし(key, value,比較演算子それぞれ)
    // 値が異常値
    // 値の入力数が大きい
    // keyの指定正規表現外
    // 比較演算子が指定値以外
}
