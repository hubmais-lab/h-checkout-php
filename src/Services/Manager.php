<?php
namespace Hubmais\HCheckout\Services;

use Hubmais\HCheckout\Client;

class Manager
{
    public function __construct(protected Client $client)
    {
    }

    public function buyers(): BuyerService
    {
        return new BuyerService($this->client);
    }

    public function transactions(): TransactionService
    {
        return new TransactionService($this->client);
    }

    public function fees(): FeeService
    {
        return new FeeService($this->client);
    }
}