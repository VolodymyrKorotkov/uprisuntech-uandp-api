<?php
namespace App\Enum;

enum CertificateStatus: string
{
    case STATUS_ACTIVE = 'valid';
    case STATUS_INACTIVE = 'invalid';
    case STATUS_REVIEW = 'review';
}