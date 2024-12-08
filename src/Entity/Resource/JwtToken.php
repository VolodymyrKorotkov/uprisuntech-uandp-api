<?php

declare(strict_types=1);

namespace App\Entity\Resource;

use ApiPlatform\Metadata\Post;
use App\Enum\AppRoutePrefixEnum;
use Symfony\Component\Serializer\Annotation\Groups;

#[Post(
    uriTemplate: '/jwt/refresh-token',
    formats: ['json'],
    routePrefix: AppRoutePrefixEnum::API_PUBLIC->value,
    denormalizationContext: ['groups' => self::REFRESH_TOKEN_GROUP],
    name: 'api.users.jwt.refresh-token'
)]

#[Post(
    uriTemplate: '/jwt/create-by-code',
    formats: ['json'],
    routePrefix: AppRoutePrefixEnum::API_PUBLIC->value,
    input: JwtCreateTokenByCode::class,
    name: self::CREATE_BY_CODE_ROUTE_NAME
)]
#[Post(
    uriTemplate: '/jwt/login',
    formats: ['json'],
    routePrefix: AppRoutePrefixEnum::API_PUBLIC->value,
    input: JwtCreateTokenByCredentials::class,
    name: self::LOGIN_BY_CREDENTIALS
)]
final readonly class JwtToken
{
    public const REFRESH_TOKEN_GROUP = 'refreshToken';
    public const CREATE_BY_CODE_ROUTE_NAME = '_api_user-service/jwt/create-by-code_post';
    public const LOGIN_BY_CREDENTIALS = '_api_user-service/jwt/login';


    #[Groups(self::REFRESH_TOKEN_GROUP)]
    public string $refreshToken;
    public string $token;
}
