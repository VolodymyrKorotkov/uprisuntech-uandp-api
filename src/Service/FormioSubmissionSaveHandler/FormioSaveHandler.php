<?php declare(strict_types=1);

namespace App\Service\FormioSubmissionSaveHandler;

use App\Service\FormioGetOrCreateProvider;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveRequestDto;
use App\Service\FormioSubmissionSaveHandler\Dto\HandleSubmissionSaveDto;
use App\Service\FormioSubmissionSaveHandler\Handler\FormioSubmissionSaveHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Throwable;

final readonly class FormioSaveHandler
{
    /**
     * @param FormioGetOrCreateProvider $formioGetOrCreateProvider
     * @param iterable<FormioSubmissionSaveHandlerInterface> $handlers
     */
    public function __construct(
        private FormioGetOrCreateProvider $formioGetOrCreateProvider,
        #[TaggedIterator(FormioSubmissionSaveHandlerInterface::class)]private iterable $handlers
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function handleSubmissionCreate(HandleSubmissionSaveRequestDto $dto): HandleSubmissionSaveRequestDto
    {
        return $this->handleSubmission(
            dto: $dto,
            handlerClasses: FormioSubmissionSaveHandlerInterface::SORTED_SUBMISSION_CREATE_HANDLERS
        );
    }

    /**
     * @throws Throwable
     */
    public function handleSubmissionEdit(HandleSubmissionSaveRequestDto $dto): HandleSubmissionSaveRequestDto
    {
        return $this->handleSubmission(
            dto: $dto,
            handlerClasses: FormioSubmissionSaveHandlerInterface::SORTED_SUBMISSION_EDIT_HANDLERS
        );
    }

    /**
     * @param HandleSubmissionSaveRequestDto $dto
     * @param array $handlers
     * @return HandleSubmissionSaveRequestDto
     */
    private function handleSubmission(HandleSubmissionSaveRequestDto $dto, array $handlerClasses): HandleSubmissionSaveRequestDto
    {
        $handlers = [];
        foreach ($this->handlers as $handler) {
            $handlers[$handler::class] = $handler;
        }

        $handleDto = new HandleSubmissionSaveDto(
            formioWebHookRequest: $dto,
            formIo: $this->formioGetOrCreateProvider->getOrCreateByFormKey($dto->formKey)
        );

        foreach ($handlerClasses as $handlerClass) {
            $handleDto->formioWebHookRequest = $handlers[$handlerClass]->handle($handleDto);
        }

        return $handleDto->formioWebHookRequest;
    }
}
