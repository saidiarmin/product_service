<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ProductController extends AbstractController
{
    private ProductServiceInterface $productService;
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(): JsonResponse
    {
        $products = $this->productService->getProducts();

        return $this->json($products);
    }

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);
        $product = new Product();
        $product->setName($requestBody['name']);
        $product->setType($requestBody['type']);
        $product->setPrice($requestBody['price']);

        $this->productService->createProduct($product);

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route('/products/{id}', name: 'update_product', methods: ['PUT', 'PATCH'])]
    public function updateProduct(Product $product, Request $request): JsonResponse
    {
        $requestBody = json_decode($request->getContent(), true);

        $product->setName($requestBody['name']);
        $product->setType($requestBody['type']);
        $product->setPrice($requestBody['price']);

        $this->productService->updateProduct();

        return $this->json($product);
    }

    #[Route('/products/{id}', name: 'remove_product', methods: ['DELETE'])]
    public function removeProduct(Product $product): JsonResponse
    {
        $this->productService->removeProduct($product);

        return $this->json($product);
    }

}
