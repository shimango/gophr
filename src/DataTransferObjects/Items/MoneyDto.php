<?php

namespace Shimango\Gophr\DataTransferObjects\Items;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class MoneyDto extends AbstractDataTransferObject
{
    public float $amount;

    public string $currency;
}
