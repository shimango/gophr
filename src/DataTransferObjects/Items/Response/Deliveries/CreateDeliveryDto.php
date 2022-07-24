<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Dropoffs\DropoffDto;
use Shimango\Gophr\DataTransferObjects\Pickups\PickupDto;

class CreateDeliveryDto extends AbstractDataTransferObject
{
    public string $delivery_id;
    public ?string $status;
    public ?string $leg_type;
    public ?string $private_job_url;
    public ?string $public_tracker_url;

    public PickupDto $pickup;
    public DropoffDto $dropoff;

    /** @var \Shimango\Gophr\DataTransferObjects\Items\ListParcelItem[] */
    public $parcels;
}
