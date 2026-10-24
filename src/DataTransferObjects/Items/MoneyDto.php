<?php

namespace Shimango\Gophr\DataTransferObjects\Items;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

final class MoneyDto extends AbstractDataTransferObject
{
    public ?float $amount = null;

    public ?string $currency = null;
}
