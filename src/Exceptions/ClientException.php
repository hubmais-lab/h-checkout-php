<?php
namespace Hubmais\HCheckout\Exceptions;

class ClientException extends \Exception
{
    protected array $context = [];

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        array $context = []
    )
    {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }

    public function context(): array
    {
        return $this->context;
    }

}