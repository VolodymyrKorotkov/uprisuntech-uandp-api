<?php

namespace App\Service\GuideBook;

use App\Repository\GuideBookRepository;
use App\Service\GuideBook\Dto\GuideBookDelete;
use App\Service\GuideBook\Dto\GuideBookToggle;

class GuideBookService implements GuideBookInterface
{
    private GuideBookRepository $guideBookRepository;

    public function __construct(
        GuideBookRepository $guideBookRepository
    )
    {
       $this->guideBookRepository = $guideBookRepository;
    }

    public function toggleGuideBook(GuideBookToggle $dto): \App\Service\GuideBook\Enum\GuideBookEnableEnum
    {
        $isEnable = false;
        if($dto->toggle === \App\Service\GuideBook\Enum\GuideBookEnableEnum::ENABLE) {
            $isEnable = true;
        }
        $this->guideBookRepository->setEnableStatus($dto->id, $isEnable);
        return $dto->toggle;
    }

    public function deleteGuideBook(GuideBookDelete $dto): void
    {
        $this->guideBookRepository->softDelete($dto->id);
    }


}