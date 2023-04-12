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
     * 文書からインスタンス生成
     *
     * @param Document $document
     * @return DocumentFile
     */
    public static function fromDocument($document)
    {
        return new self(
            $document->document_number,
            $document->document_name,
            $document->document_mime_type,
            $document->document_extension,
            $document->document_path
        );
    }

    /**
     * ファイル登録
     *
     * @param  DocumentUploadRequest $request
     * @return DocumentFile
     */
    public static function storeFile($file)
    {
        $documentNumber = Str::uuid();
        $documentExtension = $file->getClientOriginalExtension();
        $documentName = basename($file->getClientOriginalName(), '.' . $documentExtension);
        $documentMimeType = $file->getMimeType();
        $fileName = self::generateFileName($documentNumber, $documentExtension);

        // ファイル保存し、パスを取得
        $documentPath = $file->storeAs(self::STORAGE_DIR, $fileName);
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
    public function delete()
    {
        Storage::delete($this->documentPath);
    }

    /**
     * 文書のファイルを更新
     * 更新後にインスタンスを返却
     *
     * @param  DocumentFileUpdateRequest $request
     * @param  object                    $document
     * @return DocumentFile
     */
    public static function fileUpdate($file, $document)
    {
        $documentExtension = $file->getClientOriginalExtension();
        $documentName = basename($file->getClientOriginalName(), '.' . $documentExtension);

        // ファイル保存し、パスを取得
        Storage::delete($document->document_path);
        $fileName = self::generateFileName($document->document_number, $documentExtension);
        $documentPath = $file->storeAs(self::STORAGE_DIR, $fileName);
        return new self(
            $document->document_number,
            $documentName,
            $document->document_mime_type,
            $document->document_extension,
            $documentPath
        );
    }
}
