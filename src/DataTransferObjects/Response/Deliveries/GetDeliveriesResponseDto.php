<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries\GetDeliveryDto;

class GetDeliveriesResponseDto extends AbstractDataTransferObject
{
    public ?GetDeliveryDto $data;
}
