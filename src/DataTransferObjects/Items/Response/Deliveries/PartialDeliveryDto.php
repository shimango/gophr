<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class PartialDeliveryDto extends AbstractDataTransferObject
{
    public string $delivery_id;
    public ?string $status;
    public ?string $leg_type;
    public ?string $private_job_url;
    public ?string $public_tracker_url;

    public int $pickup_sequence_number;
    public int $dropoff_sequence_number;

    /** @var \Shimango\Gophr\DataTransferObjects\Items\ListParcelItem[] */
    public $parcels;
}
