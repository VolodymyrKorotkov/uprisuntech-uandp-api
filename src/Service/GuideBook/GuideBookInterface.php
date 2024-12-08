<?php

namespace App\Service\GuideBook;

use App\Service\GuideBook\Dto\GuideBookDelete;
use App\Service\GuideBook\Dto\GuideBookToggle;
use App\Service\GuideBook\Enum\GuideBookEnableEnum;

interface GuideBookInterface
{
    public const GUIDE_BOOK_ID_URL_PARAMETER_NAME = 'id';
    public const GUIDE_BOOK_ENABLE_URL_PARAMETER_NAME = 'status';

    public function toggleGuideBook(GuideBookToggle $dto): GuideBookEnableEnum;
    public function deleteGuideBook(GuideBookDelete $dto): void;
}