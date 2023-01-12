<?php

declare(strict_types=1);

namespace App\Exception;

class ConnectionException extends AbstractException implements ExceptionInterface
{
    protected function getReason(): string
    {
        return 'Connection error';
    }
}
