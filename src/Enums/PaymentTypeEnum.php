<?php
namespace Hubmais\HCheckout\Enums;

enum PaymentTypeEnum : string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
    case PIX = 'pix';
    case BOLETO = 'boleto';
    case LINK = 'link';
}