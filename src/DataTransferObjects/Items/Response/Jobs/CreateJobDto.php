<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\MoneyDto;

class CreateJobDto extends AbstractDataTransferObject
{
    public string $job_id;
    public ?int $is_confirmed;
    public ?int $vehicle_type;
    public MoneyDto $price_net;
    public MoneyDto $price_gross;
    public ?int $job_priority;
    public ?float $distance;

    /** @var \Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries\PartialDeliveryDto[] */
    public $deliveries;
}
