<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentFile
{

    /**
     * 保存ディレクトリ
     */
    const STORAGE_DIR = 'public';

    /**
     * 文書番号
     *
     * @var string
     */
    private $documentNumber;

    /**
     * 元の文書名
     *
     * @var string
     */
    private $documentName;

    /**
     * mimeタイプ
     *
     * @var string
     */
    private $documentMimeType;

    /**
     * ファイル拡張子
     *
     * @var string
     */
    private $documentExtension;

    /**
     * ファイルのパス
     *
     * @var string
     */
    private $documentPath;

    /**
     * 登録するファイル情報をセット
     *
     * @param string $path
     * @param string $documentNumber
     * @param string $mimeType
     */
    public function __construct(
        string $documentNumber,
        string $documentName,
        string $documentMimeType,
        string $documentExtension,
        string $documentPath
    ) {
        if (!Storage::exists($documentPath)) {
            throw new FileNotFoundException($documentPath);
        }

        $this->documentNumber = $documentNumber;
        $this->documentName = $documentName;
        $this->documentMimeType = $documentMimeType;
        $this->documentExtension = $documentExtension;
        $this->documentPath = $documentPath;
    }

    /**
     * ファイル登録
     *
     * @param  DocumentUploadRequest $request
     * @return DocumentFileUpload
     */
    public static function storeFile($request)
    {
        $documentNumber = Str::uuid();
        $documentExtension = $request->file('file')->getClientOriginalExtension();
        $documentName = basename($request->file('file')->getClientOriginalName(), '.' . $documentExtension);
        $documentMimeType = $request->file('file')->getMimeType();
        $fileName = self::generateFileName($documentNumber, $documentExtension);

        // ファイル保存し、パスを取得
        $documentPath = $request->file('file')->storeAs(self::STORAGE_DIR, $fileName);
        return new self($documentNumber, $documentName, $documentMimeType, $documentExtension, $documentPath);
    }

    /**
     * 保存用のファイル名生成
     *
     * @param  string $documentNumber
     * @param  string $fileExtension
     * @return string
     */
    private static function generateFileName($documentNumber, $fileExtension)
    {
        return sprintf('%s.%s', $documentNumber, $fileExtension);
    }

    public function documentNumber()
    {
        return $this->documentNumber;
    }

    public function documentName()
    {
        return $this->documentName;
    }

    public function documentMimeType()
    {
        return $this->documentMimeType;
    }

    public function documentExtension()
    {
        return $this->documentExtension;
    }

    public function path()
    {
        return $this->documentPath;
    }

    /**
     * ファイルの削除
     *
     * @param  string $documentPath
     * @return void
     */
    public static function delete($documentPath)
    {
        Storage::delete($documentPath);
    }
}
