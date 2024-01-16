<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    private ProductRepository $productRepository;
    private UserRepository $userRepository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->productRepository = $entityManager->getRepository(Product::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    public function testGetProducts(): void
    {
        $this->client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('json');

        $this->testPaginatedResponseFormat();

        $this->client->request('GET', '/api/products?page=2');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame('json');

        $this->testPaginatedResponseFormat();

        $this->client->request('GET', '/api/products?page=hello');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->client->request('GET', '/api/products?page=-2');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

    }

    public function testGetProduct(): void
    {
        // Retrieve a todo from the database
        $product = $this->productRepository->findOneBy([]);

        // Make the request
        $this->client->request('GET', "/api/products/{$product->getId()}");

        // Check if it's successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseFormatSame("json");

        // Check the response format
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testProductFormat($result);
    }

    public function testCreateTodo(): void
    {
        $newProduct = [
            'name' => 'new product',
            'type' => 'new type',
            'price' => 12.10
        ];
        // Make the request with body paramater without the "X-AUTH-TOKEN" header to chech the security
        $this->client->request('POST', '/api/products', content: json_encode($newProduct));

        // Check if the response status code is "401 Unauthorized"
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        // Retrieve a user from the database
        $user = $this->userRepository->findOneBy([]);

        // Make the request with the token header and the same body parameter
        $this->client->request(
            'POST',
            '/api/products',
            server: [
                'HTTP_X_AUTH_TOKEN' => $user->getToken()
            ],
            content: json_encode($newProduct)
        );

        // Check if the response if successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        // Check the response format
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testProductFormat($result);

        $this->assertSame('new product', $result['name']);
        $this->assertSame('new type', $result['type']);
        $this->assertSame(12.10, $result['price']);
    }

    public function testDeleteTodo(): void
    {
        // As for the previous method, we first make the request without the token header
        $product = $this->productRepository->findOneBy([]);
        $this->client->request('DELETE', "/api/products/{$product->getId()}");

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        // Make the request with the token header
        $user = $this->userRepository->findOneBy([]);
        $this->client->request(
            'DELETE',
            "/api/products/{$product->getId()}",
            server: [
                'HTTP_X_AUTH_TOKEN' => $user->getToken()
            ],
        );

        // Check if the request is successful
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function testPartialUpdate(): void
    {
        $product = $this->productRepository->findOneBy([]);
        $this->client->request('PATCH', "/api/products/{$product->getId()}");

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $user = $this->userRepository->findOneBy([]);
        $this->client->request(
            'PATCH',
            "/api/products/{$product->getId()}",
            server: [
                'HTTP_X_AUTH_TOKEN' => $user->getToken()
            ],
            content: json_encode(['name' => 'Updated name'])
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testProductFormat($result);

        $this->assertSame('Updated name', $result['name']);
    }

    public function testFullUpdate(): void
    {
        $product = $this->productRepository->findOneBy([]);
        $this->client->request('PUT', "/api/products/{$product->getId()}");

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $user = $this->userRepository->findOneBy([]);

        // Missing parameter
        $this->client->request(
            'PUT',
            "/api/products/{$product->getId()}",
            server: [
                'HTTP_X_AUTH_TOKEN' => $user->getToken()
            ],
            content: json_encode(['name' => 'Updated name'])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);


        $updatedProduct = [
            'name' => 'Updated name',
            'type' => 'Updated type',
            'price' => 15.10
        ];
        // Valid request
        $this->client->request(
            'PUT',
            "/api/products/{$product->getId()}",
            server: [
                'HTTP_X_AUTH_TOKEN' => $user->getToken()
            ],
            content: json_encode($updatedProduct)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);
        $this->testProductFormat($result);

        $this->assertSame('Updated name', $result['name']);
        $this->assertSame('Updated type', $result['type']);
        $this->assertSame(15.10, $result['price']);
    }

    private function testPaginatedResponseFormat(): void
    {
        $response = $this->client->getResponse();
        $result = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);

        foreach ($result['data'] as $product) {
            $this->testProductFormat($product);
        }

        $this->assertArrayHasKey('pagination', $result);
        $this->assertIsArray($result['pagination']);

        $paginationKeys = ["total", "count", "offset", "items_per_page", "total_pages", "current_page", "has_next_page", "has_previous_page", ];
        foreach ($paginationKeys as $key) {
            $this->assertArrayHasKey($key, $result["pagination"]);
        }
    }

    private function testProductFormat(array $productAsArray): void
    {
        $productKeys = ['id', 'name', 'type', 'price'];
        foreach ($productKeys as $key) {
            $this->assertArrayHasKey($key, $productAsArray);
        }
    }
}
