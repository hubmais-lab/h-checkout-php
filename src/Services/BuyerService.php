<?php
namespace Hubmais\HCheckout\Services;

use Hubmais\HClient\Support\EndpointBuilder;

class BuyerService extends BaseService
{
    protected function getBasePath(): string
    {
        return EndpointBuilder::marketplaceSellerPath($this->client, 'buyers');
    }

    public function find(string $doc): ?array
    {
        $response = $this->client->get($this->getBasePath(), ['doc' => $doc, 'limit' => 1]);
        return @$response['data'][0];
    }

    public function list(
        int $page = 1,
        int $limit = 20,
        ?string $search = null
    ): array
    {
        return $this->client->get($this->getBasePath(), array_filter(get_defined_vars()));
    }

    public function save(
        string $doc,
        string $name,
        string $birthdate,
        string $email,
        string $cell_number,
        array $address,
        ?string $description = null,
        ?string $phone_number = null,
        ?array $medias = null,
    ): array
    {
        return $this->client->post($this->getBasePath(), array_filter(get_defined_vars()));
    }
}
