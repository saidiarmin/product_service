<?php

namespace App\Tests\OptionsResolver;

use App\OptionsResolver\ProductOptionsResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ProductOptionsResolverTest extends TestCase
{
    private ProductOptionsResolver $optionsResolver;

    protected function setUp(): void
    {
        $this->optionsResolver = new ProductOptionsResolver();
    }

    public function testRequiredName(): void
    {
        $param = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver->configureName()->resolve($param);
    }

    public function testValidName(): void
    {
        $param = ['name' => 'test name'];

        $result = $this->optionsResolver->configureName()->resolve($param);

        $this->assertEquals('test name', $result['name']);
    }

    public function testInValidName(): void
    {
        $param = ['name' => 3];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver->configureName()->resolve($param);
    }

    public function testRequiredType(): void
    {
        $param = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver->configureType()->resolve($param);
    }

    public function testValidType(): void
    {
        $param = ['type' => 'test type'];

        $result = $this->optionsResolver->configureType()->resolve($param);

        $this->assertEquals('test type', $result['type']);
    }

    public function testInValidType(): void
    {
        $param = ['type' => 3];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver->configureType()->resolve($param);
    }

    public function testRequiredPrice(): void
    {
        $param = [];

        $this->expectException(MissingOptionsException::class);

        $this->optionsResolver->configurePrice()->resolve($param);
    }

    public function testValidPrice(): void
    {
        $param = ['price' => 12.23];

        $result = $this->optionsResolver->configurePrice()->resolve($param);

        $this->assertEquals(12.23, $result['price']);
    }

    public function testInValidPrice(): void
    {
        $param = ['price' => '34'];

        $this->expectException(InvalidOptionsException::class);

        $this->optionsResolver->configurePrice()->resolve($param);
    }
}
