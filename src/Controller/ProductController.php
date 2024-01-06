<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product')]
    public function index(): JsonResponse
    {
        $products = [
            [
                'name' => 'iphone',
                'type' => 'mobile',
                'price' => 999.99
            ],
            [
                'name' => 'lenovo',
                'type' => 'laptop',
                'price' => 1199.99
            ],
            [
                'name' => 'dell',
                'type' => 'monitor',
                'price' => 249.99
            ]
        ];

        return $this->json($products, Response::HTTP_OK);
    }
}
