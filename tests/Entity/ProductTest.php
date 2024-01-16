<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $this->validator = self::getContainer()->get('validator');
    }

    public function testDefaultValues(): void
    {
        $product = new Product();

        $this->assertNull($product->getId());
        $this->assertNull($product->getName());
        $this->assertNull($product->getType());
        $this->assertNull($product->getPrice());
    }

    public function testName(): void
    {
        $product = new Product();

        $errors = $this->validator->validateProperty($product, 'name');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $product->setName('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas');

        $errors = $this->validator->validateProperty($product, 'name');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $name = 'test';
        $product->setName($name);
        $this->assertEquals($name, $product->getName());
    }

    public function testType(): void
    {
        $product = new Product();

        $errors = $this->validator->validateProperty($product, 'type');
        $this->assertInstanceOf(NotBlank::class, $errors[0]->getConstraint());

        $product->setType('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas');

        $errors = $this->validator->validateProperty($product, 'type');
        $this->assertInstanceOf(Length::class, $errors[0]->getConstraint());

        $type = 'test type';
        $product->setType($type);
        $this->assertEquals($type, $product->getType());
    }

    public function testPrice(): void
    {
        $product = new Product();

        $price = 12.15;
        $product->setPrice($price);
        $this->assertSame($price, $product->getPrice());
    }
}
