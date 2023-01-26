<?php

namespace App\Tests\Exception;

use App\Exception\NotFoundException;
use App\Exception\ConnectionException;
use App\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

class ProductExceptionTest extends TestCase
{
    public function exceptionDataProvider(): \Generator
    {
        yield [
            new NotFoundException(),
            'Not Found',
        ];
        yield [
            new ConnectionException(),
            'Server Connection Error',
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
