<?php
namespace Hubmais\HCheckout\Enums;

enum OperationTypeEnum : string
{
    case ONLINE = 'online';
    case PRESENTIAL = 'presential';
    case TAP = 'tap';
}