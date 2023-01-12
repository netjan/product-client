<?php

declare(strict_types=1);

namespace App\Exception;

class ApiClientException extends AbstractException implements ExceptionInterface
{
    protected function getReason(): string
    {
        return 'API error';
    }
}
