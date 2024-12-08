<?php
namespace App\Enum;

enum CertificateSource: string
{
    case SOURCE_UANDP = 'course';
    case SOURCE_USER_UPLOADED = 'custom';
    case SOURCE_ORGANIZATION = 'organization';
    case SOURCE_EXTERNAL = 'external';
}