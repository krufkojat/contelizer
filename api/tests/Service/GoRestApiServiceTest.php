<?php

namespace App\Tests\Service;

use App\Service\GoRestApiService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GoRestApiServiceTest extends TestCase
{
    private HttpClientInterface $httpClient;
    private GoRestApiService $apiService;
    private string $apiToken = 'test_token';

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->apiService = new GoRestApiService($this->httpClient, $this->apiToken);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testFindUsers_WithValidParams_ReturnsSuccess(): void
    {
        $params = ['page' => 1, 'name' => 'Test'];
        $responseData = [
            ['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com']
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')->willReturn($responseData);
        $response->method('getHeaders')->willReturn([
            'x-pagination-total' => [100],
            'x-pagination-pages' => [10]
        ]);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://gorest.co.in/public/v2/users',
                [
                    'query' => $params,
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiToken,
                        'Accept' => 'application/json',
                    ],
                ]
            )
            ->willReturn($response);

        $result = $this->apiService->findUsers($params);

        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertEquals($responseData, $result['data']);
        $this->assertEquals(['total' => 100, 'pages' => 10], $result['pagination']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testFindUser_WithValidId_ReturnsUserData(): void
    {
        $userId = 1;
        $userData = [
            'id' => 1,
            'name' => 'Test User',
            'email' => 'test@example.com',
            'gender' => 'male',
            'status' => 'active'
        ];

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')->willReturn($userData);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://gorest.co.in/public/v2/users/' . $userId,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiToken,
                        'Accept' => 'application/json',
                    ],
                ]
            )
            ->willReturn($response);

        $result = $this->apiService->findUser($userId);

        $this->assertIsArray($result);
        $this->assertEquals($userData, $result);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateUser_WithValidData_ReturnsUpdatedUser(): void
    {
        $userId = 1;
        $userData = [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
            'gender' => 'male',
            'status' => 'active'
        ];

        $updatedData = array_merge(['id' => $userId], $userData);

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(Response::HTTP_OK);
        $response->method('toArray')->willReturn($updatedData);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'PUT',
                'https://gorest.co.in/public/v2/users/' . $userId,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiToken,
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                    'json' => $userData
                ]
            )
            ->willReturn($response);

        $result = $this->apiService->updateUser($userId, $userData);

        $this->assertIsArray($result);
        $this->assertEquals($updatedData, $result);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testFindUser_WithNonExistentId_ReturnsNotFoundError(): void
    {
        $userId = 99999;

        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \Exception('Resource not found', Response::HTTP_NOT_FOUND));

        $result = $this->apiService->findUser($userId);

        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertEquals('Resource not found', $result['error']);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $result['code']);
    }
}
