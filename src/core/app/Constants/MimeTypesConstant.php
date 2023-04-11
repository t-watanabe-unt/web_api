<?php

namespace App\Constants;

class MimeTypesConstant
{
    public const FILE_EXTENSIONS = [
        'pdf',
        'docx',
        'doc',
        'dot',
        'xlsx',
        'xls',
        'png',
        'jpeg',
        'jpg',
        'bmp',
        'svg',
        'svgz',
        'mp3',
        '3gp',
        'mp4',
        'mpeg',
        'qt',
        'mov',
        'webm',
        'avi'
    ];

    // mimeタイプチェック用
    public const FILE_MIME_TYPES = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'image/png',
        'image/jpeg',
        'image/bmp',
        'image/x-ms-bmp',
        'image/svg+xml',
        'audio/mpeg',
        'video/3gp',
        'video/avi',
        'video/mp4',
        'video/mpeg',
        'video/quicktime',
        'video/webm',
        'video/x-msvideo',
    ];

    /**
     * 拡張子とMimeType
     */
    public const MIME_EXTENSION = [
        'pdf' => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls' => 'application/vnd.ms-excel',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'bmp' => 'image/bmp',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'mp3' => 'audio/mpeg',
        '3gp' => 'video/3gp',
        'mp4' => 'video/mp4',
        'mpeg' => 'video/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'webm' => 'video/webm',
        'avi' => 'video/x-msvideo',
    ];
}
