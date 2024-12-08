<?php

namespace App\Enum;

use Symfony\Bundle\SecurityBundle\Security;

enum ApplicationStatusEnum: string
{
    case TODO = 'TODO';
    case TO_CONFIRMATION = 'TO_CONFIRMATION';
    case CANCELED = 'CANCELED';
    case RETURNED_TO_UPDATE = 'RETURNED_TO_UPDATE';
    case CONFIRMED = 'CONFIRMED';

    /**
     * @param Security $security
     * @return array
     */
    public static function getAllowedStatuses(Security $security): array
    {
        $result = [];
        foreach (self::cases() as $statusEnum){
            if ($security->isGranted($statusEnum->getRole()->value)){
                $result[] = $statusEnum;
            }
        }

        return $result;
    }

    public function isTaskDone(): bool
    {
        return in_array($this->value, [
            self::TO_CONFIRMATION->value,
            self::CANCELED->value,
            self::RETURNED_TO_UPDATE->value,
            self::CONFIRMED->value,
        ]);
    }

    public function isApplicationDone(): bool
    {
        return in_array($this, [
            self::CANCELED,
            self::CONFIRMED,
        ]);
    }

    public function getRole(): UserRoleEnum
    {
        return match($this){
            self::CONFIRMED, self::RETURNED_TO_UPDATE => UserRoleEnum::ROLE_APPLICATION_TASK_CONFIRM,
            default => UserRoleEnum::ROLE_USER_CASE
        };
    }
}
