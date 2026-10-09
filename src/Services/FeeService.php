<?php
namespace Hubmais\HCheckout\Services;

use Hubmais\HCheckout\Enums\CardBrandEnum;
use Hubmais\HCheckout\Enums\OperationTypeEnum;
use Hubmais\HCheckout\Enums\PaymentTypeEnum;
use Hubmais\HClient\Support\EndpointBuilder;

class FeeService extends BaseService
{
    protected function getBasePath(): string
    {
        return EndpointBuilder::marketplaceSellerPath($this->client, 'plans/fee');
    }

    public function show(
        float $amount,
        PaymentTypeEnum $payment_type,
        ?int $installments = 1,
        ?CardBrandEnum $card_brand = null,
        ?bool $all_installments = true,
        ?OperationTypeEnum $operation_type = OperationTypeEnum::ONLINE
    ): array
    {
        $query = get_defined_vars();
        $query['amount'] = number_format($query['amount'], 2, '.', '');

        return $this->client->get($this->getBasePath(), $query);
    }
}