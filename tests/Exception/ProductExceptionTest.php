<?php

namespace App\Tests\Exception;

use App\Exception\ApiClientException;
use App\Exception\ConnectionException;
use App\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

class ProductExceptionTest extends TestCase
{
    public function exceptionDataProvider(): \Generator
    {
        yield [
            new ApiClientException(),
            'API error',
        ];
        yield [
            new ConnectionException(),
            'Connection error',
        ];
    }

    public function classesDataProvider(): array
    {
        return [
            [ApiClientException::class],
            [ConnectionException::class],
        ];
    }

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testGetMessage(ExceptionInterface $exception, string $message): void
    {
        self::assertSame($message, $exception->getMessage());
    }

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testImplementsExceptionInterface(ExceptionInterface $exception): void
    {
        self::assertInstanceOf(ExceptionInterface::class, $exception);
    }

    /**
     * @dataProvider classesDataProvider
     */
    public function testThrow(string $exception)
    {
        $this->expectException($exception);

        throw new $exception();
    }
}
