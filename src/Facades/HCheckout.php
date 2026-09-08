<?php
namespace Hubmais\HCheckout\Facades;

use Illuminate\Support\Facades\Facade;

class HCheckout extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'h-checkout';
    }
}