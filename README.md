# H+Checkout PHP

Esse plugin foi construído em PHP para a realização de vendas online por meio da API Hubmais.
Aqui vamos mostrar como é facil a instalação e utilização.

## Requisitos

* php (versão 7.2 ou >=8.1)
* composer (mais recente)


## Instalação

O plugin pode ser adicionado a seu projeto com o comando abaixo:

```shell
composer require hubmais/h-checkout-php
```

## Laravel

Esse plugin pode ser usado em Laravel, a partir da versão 10.
Após a instalação é necessário executar o comando abaixo:

```shell
php artisan vendor:publish --provider="Hubmais\HCheckout\Providers\HCheckoutServiceProvider"
```

Depois é necessário configurar o arquivo de configuração, presente em config/h-checkout.php com as credenciais fornecidas.

***

# Exemplos de uso

## Inicializando sem Facade

```php
<?php

use Hubmais\HCheckout\Client;
use Hubmais\HCheckout\Services\Manager;

$client = new Client('<endpoint>');
$client->setMarketplaceId('');
$client->setSellerId('');
$client->setToken('');

$HCheckout = new Manager($client);

```

## Buscando os dados de um cliente pelo CPF/CNPJ

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$buyer = HCheckout::buyers()->find('123.456.789-00');

// ou $buyer = $HCheckout->buyers()->find('123.456.789-00'); se não for inicializado via Facades.

if($buyer)
    echo "Buyer $buyer[name] exists.";
else
    echo "Buyer not exists";
```

## Salvando dados de um cliente

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$buyer = HCheckout::buyers()->save(
    doc: '123.456.789-00', # CPF ou CNPJ
    name: 'Fulano de Tal', # Nome completo
    birthdate: '1996-05-10', # Data de Nascimento
    email: 'fulanodetal@gamil.com', # E-mail
    cell_number: '(11) 99999-0000', # Celular
    address: [ # Endereço
        'postal_code' => '01310930', # CEP
        'street' => 'Av. Paulista', # Logradouro
        'number' => '100', # Número
        'complement' => null, # Complemento
        'neighborhood' => 'Bela Vista', # Bairro
        'city' => 'São Paulo', # Cidade
        'state' => 'SP' # UF - Estado
    ]
);
```

## Buscando taxas do serviço

```php
<?php

use Hubmais\HCheckout\Enums\CardBrandEnum;
use Hubmais\HCheckout\Enums\PaymentTypeEnum;
use Hubmais\HCheckout\Facades\HCheckout;

$fees = HCheckout::fees()->show(
    amount: 100.25,
    payment_type: PaymentTypeEnum::CREDIT,
    card_brand: CardBrandEnum::VISA
);

```

## Criando venda PIX

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$transaction = HCheckout::transactions()->pix(
    amount: 100.00,
    description: 'Teste'
);
```
## Tokenizar Cartão

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$card = HCheckout::transactions()->tokenizeCard(
    buyer_id: '....', // ID retornado no cadastro de cliente HCheckout::buyers()->save()
    doc: '000.000.000-00', // CPF do titular do cartão
    'card_number' => '1234 5678 9012 3456',
    'holder_name' => 'FULANO DE TAL',
    'expiration_month' => '06',
    'expiration_year' => '2028',
    'security_code' => '123'
);
```

## Criando venda Crédito

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$transaction = HCheckout::transactions()->credit(
    amount: 100.00,
    installments: 1,
    buyer: [
        'id' => '....', // ID retornado no cadastro de cliente HCheckout::buyers()->save()
    ],
    card: [
        //'id' => '....', // ID do token do cartão obtido no metodo HCheckout::transactions()->tokenizeCard() ou informar os dados do cartão na venda
        'card_number' => '1234 5678 9012 3456',
        'holder_name' => 'FULANO DE TAL',
        'expiration_month' => '06',
        'expiration_year' => '2028',
        'security_code' => '123'
    ]
);
```

## Criando Boleto

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$transaction = HCheckout::transactions()->boleto(
    amount: 100.00,
    description: 'Teste',
    buyer: [
        'id' => '....', // ID retornado no cadastro de cliente HCheckout::buyers()->save()
    ],
    expire_date: '2026-10-15',
    payment_limit_date: '2026-10-30',
    instructions: "Sr. Caixa, não receber após 30/10/2026\nApós o vencimento solicitar um novo boleto."
);
```

## Criando Link de Pagamento

```php
<?php

use Hubmais\HCheckout\Facades\HCheckout;

$transaction = HCheckout::transactions()->boleto(
    amount: 100.00,
    description: 'Teste',
    payments_accepts: ['credit','pix','boleto'],
    payment_default: 'credit'
);
```