<?php

declare(strict_types=1);

namespace Hubmais\HCheckout;

use Hubmais\HCheckout\Exceptions\ClientException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    protected string $token = '';
    public string $marketplaceId = '';
    public string $sellerId = '';

    function __construct(
        protected string $endpoint,
        protected ?int $timeout = null
    )
    {
        
    }

    function setToken(string $token)
    {
        $this->token = $token;
    }

    function setMarketplaceId(string $marketplaceId)
    {
        $this->marketplaceId = $marketplaceId;
    }

    function setSellerId(string $sellerId)
    {
        $this->sellerId = $sellerId;
    }

    protected function request(): \GuzzleHttp\Client
    {
        $endpoint = rtrim($this->endpoint, '/');

        if (empty($endpoint))
            throw new ClientException('HCheckout endpoint is not configured.', 400);

        if (empty($this->token))
            throw new ClientException('HCheckout token is not configured.', 400);

        $client = new \GuzzleHttp\Client([
            'base_uri' => $endpoint,
            'timeout' => (int) $this->timeout?:30,
            'headers' => [
                'User-Agent' => 'HCheckout Plugin PHP 1.0',
                'Accept' => 'application/json',
                'Authorization' => "Bearer $this->token"
            ],
        ]);

        return $client;
    }

    public function get(string $uri, array $query = []): array
    {
        return $this->handleResponse(
            $this->request()->request(
                'GET', 
                $uri,
                [
                    'query' => $query
                ]
            ),
            'GET',
            $uri,
            $query
        );
    }

    public function post(string $uri, array $payload = []): array
    {
        return $this->handleResponse(
            $this->request()->request('POST', $uri, ['json' => $payload]),
            'POST',
            $uri,
            $payload
        );
    }

    public function put(string $uri, array $payload = []): array
    {
        return $this->handleResponse(
            $this->request()->request('PUT', $uri, ['json' => $payload]),
            'PUT',
            $uri,
            $payload
        );
    }

    public function delete(string $uri, array $payload = []): array
    {
        return $this->handleResponse(
            $this->request()->request('DELETE', $uri, ['json' => $payload]),
            'DELETE',
            $uri,
            $payload
        );
    }

    protected function handleResponse(ResponseInterface $response, string $method, string $uri, array $data = []): array
    {
        if($response->getStatusCode() >= 200 && $response->getStatusCode() < 400)
            return json_decode($response->getBody()->getContents(), true) ?: [];
        
        $context = [
            'method' => $method,
            'uri' => $uri,
            'request_data' => $data,
            'status' => $response->getStatusCode(),
            'response_body' => $response->getBody()->getContents(),
        ];

        throw new ClientException(
            'HCheckout request failed. Status: ' . $response->getStatusCode() . '. Response: ' . $response->getBody()->getContents(),
            $response->getStatusCode(),
            null,
            $context
        );
    }
}
