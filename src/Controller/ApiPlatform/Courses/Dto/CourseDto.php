<?php

namespace App\Controller\ApiPlatform\Courses\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Controller\ApiPlatform\Courses\CourseProvider;

#[ApiResource(
    shortName: 'LMS Courses',
    operations: [
        new GetCollection()
    ],
    uriTemplate: '/courses',
    routePrefix: 'account',
    provider: CourseProvider::class,
    paginationEnabled: true,
    paginationClientItemsPerPage: true,
    paginationClientEnabled: true
)]

class CourseDto
{
    #[ApiProperty(identifier: true)]
    public $id;
    public $title;
    public $slug;
    public $postType;
    public $permalink;
    public $status;
    public $content;
    public $capacityMessage;
    public $dateCreated;

    public static function mapToDto(array $item): self
    {
        $dto = new self;
        $dto->id = $item['id'];
        $dto->title = $item['title']['rendered'] ?? '';
        $dto->slug = $item['slug'] ?? '';
        $dto->postType = $item['post_type'] ?? '';
        $dto->permalink = $item['permalink'] ?? '';
        $dto->status = $item['status'] ?? '';
        $dto->content = $item['content']['rendered'] ?? '';
        $dto->capacityMessage = $item['capacity_message']['rendered'] ?? '';
        $dto->dateCreated = $item['date_created'] ?? '';

        return $dto;
    }
}