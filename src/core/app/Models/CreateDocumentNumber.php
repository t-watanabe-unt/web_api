<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * 文書の番号生成
 */
class CreateDocumentNumber
{
    /**
     * 文書番号をuuidのver4より生成
     *
     * @return string
     */
    public static function makeByUuidVer4()
    {
        $documentNumber = Str::uuid();
        return $documentNumber;
    }
}
