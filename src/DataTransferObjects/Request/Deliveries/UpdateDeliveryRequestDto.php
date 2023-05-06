<?php

namespace Shimango\Gophr\DataTransferObjects\Request\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Dropoffs\DropoffDto;
use Shimango\Gophr\DataTransferObjects\Pickups\PickupDto;

class UpdateDeliveryRequestDto extends AbstractDataTransferObject
{
    public PickupDto $pickup;

    public DropoffDto $dropoff;
}
