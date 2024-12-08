<?php

namespace App\Service\KeycloakClient\Dto;

class SearchUserDto
{
    public ?bool $emailVerified = null; //whether the email has been verified
    public ?string $email = null; //A String contained in email, or the complete email, if param "exact" is true
    public ?bool $enabled = null; //Boolean representing if user is enabled or not
    public ?bool $exact = null; //Boolean which defines whether the params "last", "first", "email" and "username" must match exactly
    public ?string $username = null; //A String contained in username, or the complete username, if param "exact" is true
    public ?string $firstName = null; //A String contained in firstName, or the complete firstName, if param "exact" is true
    public ?string $lastName = null; //A String contained in lastName, or the complete lastName, if param "exact" is true
    public ?string $idpAlias = null; //The alias of an Identity Provider linked to the user
    public ?string $idpUserId = null; //The userId at an Identity Provider linked to the user
    public ?string $q = null; //A query to search for custom attributes, in the format 'key1:value2 key2:value2'
    public ?string $search = null; //A String contained in username, first or last name, or email

    public function toQuery(): string {
        $queryParams = [];
        foreach ((array) $this as $key => $value) {
            if($value !== null) {
                $queryParams[$key] = $value;
            }
        }
        return http_build_query($queryParams);
    }
}
