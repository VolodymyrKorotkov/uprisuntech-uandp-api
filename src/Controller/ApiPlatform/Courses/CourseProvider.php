<?php
namespace App\Controller\ApiPlatform\Courses;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Controller\ApiPlatform\Courses\Dto\CourseDto;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CourseProvider implements ProviderInterface
{
    private const API_URL_COURSES = "https://staging-ndp.netvision.pro/wp-json/llms/v1/courses?order=desc&orderBy=date_created&per_page=%d&page=%d";
    private const DEFAULT_PER_PAGE = 20;
    private const DEFAULT_PAGE_NUMBER = 1;

    public function __construct(
        private HttpClientInterface $client
    ){}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $courses = $this->mapCourses(
            $this->fetchCoursesFromApi($this->createUrl($context))
        );

        return $courses;
    }

    private function fetchCoursesFromApi(string $url): string
    {
        $response = $this->client->request('GET', $url);

        return $response->getContent();
    }

    private function createUrl(array $context = []): string
    {
        $page = $context['filters']['page'] ?? self::DEFAULT_PAGE_NUMBER;
        $perPage = $context['filters']['itemsPerPage'] ?? self::DEFAULT_PER_PAGE;

        return sprintf(self::API_URL_COURSES, $perPage, $page);
    }

    private function mapCourses(string $responseContent): array
    {
        $coursesData = json_decode($responseContent, true);
        $courses = array_map([CourseDto::class, 'mapToDto'], $coursesData);

        return $courses;
    }
}
