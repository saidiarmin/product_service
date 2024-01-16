<?php

namespace App\Tests\Repository;

use App\Entity\Product;
use App\Model\Paginator;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    private ProductRepository $repository;
    public function setUp(): void
    {
        $em = self::getContainer()->get("doctrine")->getManager();
        $this->repository = $em->getRepository(Product::class);
    }

    public function testFindAllWithPagination(): void
    {
        $result = $this->repository->findAllWithPagination(1);

        $this->assertInstanceOf(Paginator::class, $result);
        $this->assertEquals(1, $result->getCurrentPage());
    }
}
