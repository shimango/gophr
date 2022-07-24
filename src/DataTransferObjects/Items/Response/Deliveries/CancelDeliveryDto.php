<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\MoneyDto;

class CancelDeliveryDto extends AbstractDataTransferObject
{
    public ?MoneyDto $cancelation_cost;
}
