<?php

namespace App\Controller;

use App\Entity\Product;
use App\OptionsResolver\PaginatorOptionsResolver;
use App\OptionsResolver\ProductOptionsResolver;
use App\Service\ProductServiceInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_', format: 'json')]
class ProductController extends AbstractController
{
    private ProductServiceInterface $productService;
    private ValidatorInterface $validator;
    public function __construct(ProductServiceInterface $productService, ValidatorInterface $validator)
    {
        $this->productService = $productService;
        $this->validator = $validator;
    }

    #[Route('/products', name: 'get_products', methods: ['GET'])]
    public function getProducts(Request $request, PaginatorOptionsResolver $paginatorOptionsResolver): JsonResponse
    {
        try {
            $queryParams = $paginatorOptionsResolver
                ->configurePage()
                ->resolve($request->query->all());

            $products = $this->productService->getProductsWithPagination($queryParams['page']);

            return $this->json($products);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function createProduct(Request $request, ProductOptionsResolver $productOptionsResolver): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);

            $fields = $productOptionsResolver
                ->configureName(true)
                ->configureType(true)
                ->configurePrice(true)
                ->resolve($requestBody);

            $product = new Product();
            $product->setName($fields['name']);
            $product->setType($fields['type']);
            $product->setPrice($fields['price']);

            $errors = $this->validator->validate($product);
            if (count($errors) > 0) {
                throw new BadRequestHttpException((string)$errors);
            }

            $this->productService->createProduct($product);

            return $this->json($product, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/products/{id}', name: 'update_product', methods: ['PUT', 'PATCH'])]
    public function updateProduct(Product $product, Request $request, ProductOptionsResolver $productOptionsResolver): JsonResponse
    {
        try {
            $requestBody = json_decode($request->getContent(), true);
            $isPutMethod = $request->getMethod() === 'PUT';

            $fields = $productOptionsResolver
                ->configureName($isPutMethod)
                ->configureType($isPutMethod)
                ->configurePrice($isPutMethod)
                ->resolve($requestBody);

            foreach ($fields as $field => $value) {
                switch ($field) {
                    case 'name':
                        $product->setName($value);
                        break;
                    case 'type':
                        $product->setType($value);
                        break;
                    case 'price':
                        $product->setPrice($value);
                        break;
                }
            }

            $errors = $this->validator->validate($product);
            if (count($errors) > 0) {
                throw new InvalidArgumentException((string)$errors);
            }

            $this->productService->updateProduct();

            return $this->json($product);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    #[Route('/products/{id}', name: 'remove_product', methods: ['DELETE'])]
    public function removeProduct(Product $product): JsonResponse
    {
        $this->productService->removeProduct($product);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
