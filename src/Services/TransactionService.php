<?php
namespace Hubmais\HCheckout\Services;

use Hubmais\HCheckout\Enums\CardBrandEnum;
use Hubmais\HCheckout\Enums\PaymentTypeEnum;
use Hubmais\HCheckout\Exceptions\BoletoValidationException;
use Hubmais\HCheckout\Exceptions\BuyerValidationException;
use Hubmais\HCheckout\Exceptions\CallbackValidationException;
use Hubmais\HCheckout\Exceptions\CardValidationException;
use Hubmais\HCheckout\Support\PayloadFormatter;
use Hubmais\HClient\Support\EndpointBuilder;

class TransactionService extends BaseService
{
    protected function getBasePath(): string
    {
        return EndpointBuilder::marketplaceSellerPath($this->client, 'transactions');
    }

    public function list(
        ?int $page = 1,
        ?int $limit = 20,
        ?array $with = null
    ): array
    {
        return $this->client->get($this->getBasePath(), array_filter(get_defined_vars()));
    }

    public function show(
        string $transactionId,
        ?array $with = null
    ): array
    {
        return $this->client->get($this->getBasePath() . '/' . $transactionId, array_filter(compact('with')));
    }

    private function validateBuyer(array $buyer)
    {
        if(!isset($buyer['id']))
        {
            $errors = [];

            foreach(['doc','name','birthdate','email','cell_number','address'] as $param)
            {
                if(empty(@$buyer[$param]))
                    $errors[] = "The parameter '$param' is invalid.";
            }

            if(count($errors))
                throw new BuyerValidationException("One or more fields the buyer are invalids:\n".implode("\n- ", $errors));
        }
    }

    private function validateCard(array $card)
    {
        if(!isset($card['id']))
        {
            $errors = [];

            foreach(['card_number', 'holder_name', 'expiration_month','expiration_year','security_code', 'brand'] as $param)
            {
                if(empty(@$card[$param]))
                    $errors[] = "The parameter '$param' is invalid.";
                elseif($param == 'card_number')
                {
                    $digits = preg_replace('/[^0-9]/', '', $card[$param]);
                    if(strlen($digits) != 16)
                        $errors[] = "The $param expected 16 digits, but encountered ".strlen($digits)." digits.";
                }
                elseif($param == 'expiration_month')
                {
                    if($card[$param] < 1 || $card[$param] > 12)
                        $errors[] = "The $param expected number between 1 and 12, but encountered $card[$param].";
                }
                elseif($param == 'expiration_year')
                {
                    if($card[$param] >= date('Y') || $card[$param] <= (date('Y') + 11))
                        $errors[] = "The $param expected number between ".date('Y')." and ".(date('Y') + 11).", but encountered $card[$param].";
                }
                elseif($param == 'security_code')
                {
                    $digits = preg_replace('/[^0-9]/', '', $card[$param]);
                    if(strlen($digits) >= 3 && strlen($digits) <=4)
                        $errors[] = "The $param expected between 3 and 4 digits, but encountered ".strlen($digits)." digits.";
                }
                elseif($param == 'brand')
                {
                    if(!CardBrandEnum::tryFrom($card[$param]))
                        $errors[] = "The brand $card[$param] is not allowed.";
                }
            }

            if(count($errors))
                throw new CardValidationException("One or more fields the card are invalids:\n".implode("\n- ", $errors));
        }
    }

    private function validateBoleto(array $boleto)
    {
        if(isset($boleto['billings']))
        {
            $errors = [];

            foreach($boleto['billings'] as $i => $billing)
            {
                foreach(['type', 'mode', 'amount', 'percentage', 'start_date'] as $param)
                {
                    if(empty(@$billing[$param]))
                        $errors[] = "The parameter 'boleto.billings[$i].$param' is invalid.";
                }
            }

            if(count($errors))
                throw new BoletoValidationException("One or more fields the boleto are invalids:\n".implode("\n- ", $errors));
        }
    }

    private function validateCallback(array $callback)
    {
        $errors = [];

        foreach(['method', 'url'] as $param)
        {
            if(empty(@$callback[$param]))
                $errors[] = "The parameter '$param' is invalid.";
        }

        if(isset($callback['headers']))
        {
            if(!is_array($callback['headers']))
                $errors[] = "The parameter 'headers' is invalid.";
        }

        if(count($errors))
            throw new CallbackValidationException("One or more fields the card are invalids:\n".implode("\n- ", $errors));
    }

    public function tokenizeCard(
        string $buyer_id,
        string $doc,
        string $holder_name,
        string $card_number,
        string $brand,
        string $expiration_month,
        string $expiration_year,
        string $security_code
    ): array
    {
        $this->validateCard(compact('card_number', 'holder_name', 'expiration_month', 'expiration_year', 'security_code', 'brand'));

        return $this->client->post($this->getBasePath() . '/card_tokenize', get_defined_vars());
    }

    public function link(
        float $amount,
        string $description,
        array $payments_accepts,
        string $payment_default,
        ?int $expire = null,
        ?array $buyer = null,
        ?array $credit = null,
        ?string $user_agent = null,
        ?string $ip_address = null,
        ?string $callback_success = null,
        ?string $callback_failure = null,

    )
    {
        if($buyer)
            $this->validateBuyer($buyer);

        $payload = array_filter(get_defined_vars());
        $payload['payment_type'] = PaymentTypeEnum::LINK;

        $payload = PayloadFormatter::formatAmount($payload);

        return $this->client->post($this->getBasePath(), $payload);
    }

    function credit(
        float $amount,
        int $installments,
        array $buyer,
        array $card,
        string $fingerprint,
        ?string $description = null,
        ?string $user_agent = null,
        ?string $ip_address = null,
        ?array $callback = null,
    )
    {
        $this->validateBuyer($buyer);
        $this->validateCard($card);

        if($callback)
            $this->validateCallback($callback);

        $payload = array_filter(get_defined_vars());
        $payload['payment_type'] = PaymentTypeEnum::BOLETO;

        $payload = PayloadFormatter::formatAmount($payload);

        return $this->client->post($this->getBasePath(), $payload);
    }

    public function pix(
        float $amount,
        string $description,        
        ?array $callback = null,
    )
    {
        if($callback)
            $this->validateCallback($callback);

        $payload = array_filter(get_defined_vars());
        $payload['payment_type'] = PaymentTypeEnum::PIX;

        $payload = PayloadFormatter::formatAmount($payload);
        return $this->client->post($this->getBasePath(), $payload);
    }

    public function boleto(
        float $amount,
        string $description,
        array $buyer,
        string $expire_date,
        string $payment_limit_date,
        string $instructions,
        ?array $boleto = null,
        ?array $callback = null,
    ): array
    {
        $this->validateBuyer($buyer);

        if($boleto)
            $this->validateBoleto($boleto);

        if($callback)
            $this->validateCallback($callback);

        $payload = array_filter(get_defined_vars());
        $payload['payment_type'] = PaymentTypeEnum::BOLETO;

        $payload = PayloadFormatter::formatAmount($payload);
        return $this->client->post($this->getBasePath(), $payload);
    }    
}