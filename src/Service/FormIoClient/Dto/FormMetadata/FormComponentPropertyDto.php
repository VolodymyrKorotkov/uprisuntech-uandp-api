<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto\FormMetadata;

final class FormComponentPropertyDto
{
    public string|null $varKey = null;

    public function varKeyIsEmpty(): bool
    {
        return null === $this->varKey;
    }
}
