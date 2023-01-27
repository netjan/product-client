<?php

declare(strict_types=1);

namespace App\Exception;

class DataTransferException extends AbstractException implements ExceptionInterface
{
    protected function getReason(): string
    {
        return 'Data transfer error';
    }
}
