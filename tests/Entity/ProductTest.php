<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;

class ProductTest extends TestCase
{
    private Product $entityTest;

    public function setUp(): void
    {
        $this->entityTest = new Product();
    }

    public function testCreateEmptyProduct(): void
    {
        $this->assertInstanceOf(Product::class, $this->entityTest);
        $this->assertNull($this->entityTest->getId());
        $this->assertNull($this->entityTest->getName());
        $this->assertNull($this->entityTest->getAmount());

        $data = $this->entityTest->toArray();
        foreach ($data as $item) {
            $this->assertNull($item);
        }

        $product = new Product(1);
        $this->assertEquals(1, $product->getId());
    }

    public function propertyGetSet(): \Generator
    {
        yield ['name', 'StringValue'];
        yield ['amount', 1];
    }

    /**
     * @dataProvider propertyGetSet
     */
    public function testGetSet(string $propertyName, $expectedValue): void
    {
        $setMethod = 'set'.\ucfirst($propertyName);
        $this->entityTest->$setMethod($expectedValue);
        $getMethod = 'get'.\ucfirst($propertyName);
        $actual = $this->entityTest->$getMethod();
        $this->assertSame($expectedValue, $actual);
        $this->assertEquals($expectedValue, $actual);

        $data = $this->entityTest->toArray();
        foreach ($data as $key => $value) {
            if ($key === $propertyName) {
                $this->assertSame($expectedValue, $value);
                $this->assertEquals($expectedValue, $value);
            }
        }
    }

    public function attributesAssertProvider(): array
    {
        return [
            ['name', Assert\NotBlank::class, []],
            ['name', Assert\Type::class, ['string']],
            ['name', Assert\Length::class, ['max' => 255]],
            ['amount', Assert\NotBlank::class, []],
            ['amount', Assert\Type::class, ['integer']],
            ['amount', Assert\GreaterThanOrEqual::class, [0]],
        ];
    }

    /**
     * @dataProvider attributesAssertProvider
     */
    public function testAssertAttributesSetOnProperty(string $propertyName, string $expectedAttributeName, array $expectedArguments): void
    {
        $property = new \ReflectionProperty(Product::class, $propertyName);
        $result = $property->getAttributes($expectedAttributeName);

        $this->assertCount(
            1,
            $result,
            sprintf('%s::%s does not contain attribute "%s".', Product::class, $propertyName, $expectedAttributeName)
        );

        $attribute = $result[0];
        $this->assertInstanceOf(
            \ReflectionAttribute::class,
            $attribute
        );

        $this->assertSame(
            $expectedArguments,
            $attribute->getArguments()
        );
    }
}
