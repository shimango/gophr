<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries\CreateDeliveryDto;

class CreateDeliveriesResponseDto extends AbstractDataTransferObject
{
    public ?CreateDeliveryDto $data;
}
