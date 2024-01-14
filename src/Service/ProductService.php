<?php

namespace App\Service;

use App\Entity\Product;
use App\Model\Paginator;
use App\Repository\ProductRepository;

class ProductService implements ProductServiceInterface
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getProducts(): ?array
    {
        return $this->repository->findAll();
    }

    public function getProductsWithPagination(int $page): Paginator
    {
        return $this->repository->findAllWithPagination($page);
    }

    public function getProductById(int $id): ?Product
    {
        return $this->repository->find($id);
    }

    public function createProduct(Product $product): void
    {
        $this->repository->save($product);
    }

    public function removeProduct(Product $product): void
    {
        $this->repository->delete($product);
    }

    public function updateProduct(): void
    {
        $this->repository->update();
    }
}