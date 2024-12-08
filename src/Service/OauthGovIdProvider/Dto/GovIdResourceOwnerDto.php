<?php declare(strict_types=1);

namespace App\Service\OauthGovIdProvider\Dto;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class GovIdResourceOwnerDto
{
    public const DATE_FORMAT = 'Y-m-d H:i:s'; //0001-01-01

    #[SerializedName('auth_type')]
    public string|null $authType = null;
    public string|null $issuer = null;
    public string|null $issuercn = null;
    public string|null $serial = null;
    public string|null $subject = null;
    public string|null $subjectcn = null;
    public string|null $locality = null;
    public string|null $state = null;
    public string|null $country = null;
    public string|null $o = null;
    public string|null $ou = null;
    public string|null $title = null;
    public string|null $givenname = null;
    public string|null $middlename = null;
    public string|null $lastname = null;
    public string|null $email = null;
    public string|null $address = null;

    #[SerializedName('address_juridical')]
    public string|null $addressJuridical = null;
    public string|null $phone = null;
    public string|null $dns = null;
    public string|null $edrpoucode = null;
    public string|null $drfocode = null;
    public string|null $unzr = null;
    public string|null $passport = null;
    #[SerializedName('person_identifier')]
    public string|null $personIdentifier = null;
    public string|null $birthday = null;
    public string|null $documents = null;
    public string|null $gender = null;

    public mixed $jsonData = null;
}
