<?php

namespace App\Services\Mailing;

class MailerAttachmentCollection
{

    private array $collection = [];

    public function add(string $path, string $name = ''): static
    {
        $this->collection[] = [
            'path' => $path,
            'name' => $name,
        ];

        return $this;
    }

    public function notEmpty(): bool
    {
        return count($this->collection) > 0;
    }

    public function get(): array
    {
        return $this->collection;
    }

}