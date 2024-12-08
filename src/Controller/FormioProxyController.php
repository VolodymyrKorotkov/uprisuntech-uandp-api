<?php declare(strict_types=1);

namespace App\Controller;

use App\Serializer\AppJsonNormalizerInterface;
use App\Service\FormIoClient\Dto\FormSubmission\FormSubmissionDto;
use App\Service\FormIoClient\FormioProxyClient;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\FormioSaveHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

final class FormioProxyController extends AbstractController
{
    /**
     * @throws Throwable
     */
    #[Route('/formio/{formKey}/submission', methods: ['POST'])]
    public function createSubmission(
        Request                    $request,
        string                     $formKey,
        AppJsonNormalizerInterface $jsonNormalizer,
        FormioSaveHandler          $formioSaveHandler
    ): Response
    {
        $dto = new HandleSubmissionSaveRequestDto(
            submission: $jsonNormalizer->denormalize($request->toArray(), FormSubmissionDto::class),
            formKey: $formKey
        );

        try {
            return $this->json(
                $formioSaveHandler->handleSubmissionCreate($dto)->submission,
                headers: [
                    'X-Jwt-Token' => $request->headers->get('X-Jwt-Token')
                ]
            );
        } catch (Throwable $exception){
            return $this->handleException($exception);
        }
    }

    /**
     * @throws Throwable
     */
    #[Route('/formio/{formKey}/submission/{submissionID}', methods: ['PUT'])]
    public function editSubmission(
        Request                    $request,
        string                     $formKey,
        AppJsonNormalizerInterface $jsonNormalizer,
        FormioSaveHandler          $formioSaveHandler
    ): Response
    {
        $dto = new HandleSubmissionSaveRequestDto(
            submission: $jsonNormalizer->denormalize($request->toArray(), FormSubmissionDto::class),
            formKey: $formKey
        );

        try {
            return $this->json(
                $formioSaveHandler->handleSubmissionEdit($dto)->submission,
                headers: [
                    'X-Jwt-Token' => $request->headers->get('X-Jwt-Token')
                ]
            );
        }  catch (Throwable $exception){
            return $this->handleException($exception);
        }
    }

    /**
     * @param Request $request
     * @param FormioProxyClient $proxyClient
     * @return Response
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws Throwable
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('formio/{param1}/{param2}/{param3}/{param4}')]
    #[Route('/formio/{param1}/{param2}/{param3}')]
    #[Route('/formio/{param1}/{param2}')]
    #[Route('/formio/{param1}')]
    #[Route('/formio')]
    public function commonAction(Request $request, FormioProxyClient $proxyClient): Response
    {
        return $proxyClient->proxyRequest($request);
    }

    /**
     * @throws Throwable
     */
    private function handleException(Throwable $exception): JsonResponse
    {
        if ($exception instanceof ClientException) {
            return new JsonResponse(
                $exception->getResponse()->getContent(false),
                status: $exception->getResponse()->getStatusCode(),
                json: true
            );
        }

        $prev = $exception->getPrevious();
        if ($prev instanceof ClientException) {
            return new JsonResponse(
                $prev->getResponse()->getContent(false),
                status: $prev->getResponse()->getStatusCode(),
                json: true
            );
        }

        throw $exception;
    }
}
