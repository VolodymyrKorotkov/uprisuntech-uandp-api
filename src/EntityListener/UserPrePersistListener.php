<?php

namespace App\EntityListener;

use App\Entity\User;
use App\Enum\ProfileGenderEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;


#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: User::class)]
class UserPrePersistListener
{
    public function prePersist(User $user): void
    {
        return ;
        $this->handleDRFOCode($user);
        $this->generateToken($user);
    }

    private function handleDRFOCode(User $user): void
    {
        $drfoCode = $user->getDrfoCode();
        if ($drfoCode) {

            $user->setDateOfBirth(
                $this->getBirthDateFromDrfoCode($drfoCode)
            );

            $user->setGender(
                $this->getGenderFromDrfoCode($drfoCode)
            );
        }
    }

    private function getBirthDateFromDrfoCode($drfoCode): \DateTime
    {
        $daysSince1900 = intval(substr($drfoCode, 0, 5));
        return (new \DateTime('1899-12-31'))->modify("+$daysSince1900 days");
    }

    private function getGenderFromDrfoCode($drfoCode): ?string
    {
        if (strlen($drfoCode) < 9){
            return null;
        }

        $genderCode = intval($drfoCode[8]);
        return $genderCode % 2 === 0 ?
            ProfileGenderEnum::PROFILE_FEMALE->value :
            ProfileGenderEnum::PROFILE_MALE->value;
    }

    private function generateToken(User $user): void
    {
        $randomString = bin2hex(random_bytes(16));
        $user->setToken(hash('sha256', $randomString));
    }
}