<?php


namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoRestApiService
{
    private string $apiUrl = 'https://gorest.co.in/public/v2';

    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly string $apiToken
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function findUsers(array $params = []): ?array
    {
        $queryParams = [];

        if (isset($params['page'])) {
            $queryParams['page'] = $params['page'];
        }

        if (isset($params['name'])) {
            $queryParams['name'] = $params['name'];
        }

        if (isset($params['email'])) {
            $queryParams['email'] = $params['email'];
        }

        if (isset($params['gender'])) {
            $queryParams['gender'] = $params['gender'];
        }

        if (isset($params['status'])) {
            $queryParams['status'] = $params['status'];
        }

        try {
            $response = $this->client->request('GET', $this->apiUrl . '/users', [
                'query' => $queryParams,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $data = $response->toArray();
                $headers = $response->getHeaders();

                return [
                    'success' => true,
                    'pagination' => [
                        'total' => $headers['x-pagination-total'][0] ?? 0,
                        'pages' => $headers['x-pagination-pages'][0] ?? 1
                    ],
                    'data' => $data,
                ];
            }

            return null;
        } catch (Exception $e) {
            return $this->createErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function findUser(int $id): ?array
    {
        try {
            $response = $this->client->request('GET', $this->apiUrl . '/users/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                return $response->toArray();
            }

            return null;
        } catch (Exception $e) {
            return $this->createErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function updateUser(int $id, array $data): ?array
    {
        try {
            $response = $this->client->request('PUT', $this->apiUrl . '/users/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $data
            ]);

            if (in_array($response->getStatusCode(), [Response::HTTP_OK, Response::HTTP_CREATED])) {
                return $response->toArray();
            }

            return null;
        } catch (Exception $e) {
            return $this->createErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    private function createErrorResponse(string $message, int $code): array
    {
        return [
            'success' => false,
            'error' => $message,
            'code' => $code
        ];
    }
}
