<?php

namespace Shimango\Gophr\DataTransferObjects\Request\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class CreateJobRequestDto extends AbstractDataTransferObject
{
    public ?int $vehicle_type;
    public ?int $is_confirmed;
    public ?int $job_priority;
    public ?int $is_fixed_sequence;

    /** @var \Shimango\Gophr\DataTransferObjects\Pickups\PickupDto[] */
    public $pickups;

    /** @var \Shimango\Gophr\DataTransferObjects\Dropoffs\DropoffDto[] */
    public $dropoffs;

    public ?array $meta_data;
}
