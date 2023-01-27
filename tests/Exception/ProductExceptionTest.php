<?php

namespace App\Tests\Exception;

use PHPUnit\Framework\TestCase;
use App\Exception\NotFoundException;
use App\Exception\ExceptionInterface;
use App\Exception\ConnectionException;
use App\Exception\DataTransferException;

class ProductExceptionTest extends TestCase
{
    public function exceptionDataProvider(): \Generator
    {
        yield [
            new ConnectionException(),
            'Server connection error',
        ];
        yield [
            new DataTransferException(),
            'Data transfer error',
        ];
        yield [
            new NotFoundException(),
            'Not found',
        ];
    }

    public function classesDataProvider(): array
    {
        return [
            [NotFoundException::class],
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
