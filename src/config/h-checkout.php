<?php
return [
    # Endpoint the customization access
    'endpoint' => env('HCHECKOUT_ENPOINT', 'https://teste.api.payments.hubmais.tec.br'),

    # MarketplaceId provided by the provider
    'marketplace_id' => '',

    # SellerId provided by the provider
    'seller_id' => '',

    # Token access API
    'token' => '',

    # Timeout in seconds
    'timeout' => 30,
];