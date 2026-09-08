<?php

declare(strict_types=1);

namespace Hubmais\HCheckout\Support;

class PayloadFormatter
{
    public static function formatAmount(array $payload): array
    {
        if (isset($payload['amount'])) {
            $payload['amount'] = number_format((float) $payload['amount'], 2, '.', '');
        }

        return $payload;
    }
}