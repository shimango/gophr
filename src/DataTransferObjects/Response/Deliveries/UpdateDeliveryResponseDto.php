<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries\UpdateDeliveryDto;

class UpdateDeliveryResponseDto extends AbstractDataTransferObject
{
    public ?UpdateDeliveryDto $data = null;
}
