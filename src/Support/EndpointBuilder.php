<?php

declare(strict_types=1);

namespace Hubmais\HCheckout\Support;

use Hubmais\HCheckout\Client;
use Hubmais\HCheckout\Exceptions\ClientException;

class EndpointBuilder
{
    public static function marketplaceSellerPath(
        Client $client,
        string $resource
    ): string
    {
        if (empty($client->marketplaceId))
            throw new ClientException('Marketplace ID is not configured.', 400);

        if (empty($client->sellerId))
            throw new ClientException('Seller ID is not configured.', 400);

        $resource = ltrim($resource, '/');

        return "/v1/marketplaces/{$client->marketplaceId}/sellers/{$client->sellerId}/{$resource}";
    }
}