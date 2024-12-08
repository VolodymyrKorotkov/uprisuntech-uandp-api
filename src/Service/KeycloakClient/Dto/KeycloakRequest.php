<?php declare(strict_types=1);

namespace App\Service\KeycloakClient\Dto;

final readonly class KeycloakRequest
{
    public function __construct(
        public string        $method,
        public string        $path,
        public RealmAuthInfo $authUser,
        public mixed         $queryParams = [],
        public mixed         $parameters = [],
        public mixed         $body = [],
        public bool          $isJsonContent = true
    )
    {
    }

    public function hasQueryParams(): bool
    {
        return !empty($this->queryParams);
    }

    public function hasParameters(): bool
    {
        return !empty($this->parameters);
    }

    public function hasBody(): bool
    {
        return !empty($this->body);
    }
}
