<?php declare(strict_types=1);

namespace App\Service\FormIoClient\Dto;

class AbstractFormIoResponse
{
    public string $content = '{}';

    public function getContent(): string
    {
        return $this->content;
    }

    public function getArrayContent(): array
    {
        return json_decode($this->content, true);
    }
}
