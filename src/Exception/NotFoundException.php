<?php

declare(strict_types=1);

namespace App\Exception;

class NotFoundException extends AbstractException implements ExceptionInterface
{
    protected function getReason(): string
    {
        return 'Not found';
    }
}
