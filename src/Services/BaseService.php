<?php
namespace Hubmais\HCheckout\Services;

use Hubmais\HCheckout\Client;

abstract class BaseService
{
    public function __construct(protected Client $client)
    {
    }
}