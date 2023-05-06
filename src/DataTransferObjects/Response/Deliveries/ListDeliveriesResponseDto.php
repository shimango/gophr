<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries\ListDeliveriesDto;

class ListDeliveriesResponseDto extends AbstractDataTransferObject
{
    public ?ListDeliveriesDto $data = null;
}
