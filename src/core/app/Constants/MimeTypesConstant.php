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
}