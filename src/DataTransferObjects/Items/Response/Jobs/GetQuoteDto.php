<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\MoneyDto;

class GetQuoteDto extends AbstractDataTransferObject
{
    public ?int $job_priority;
    public ?int $vehicle_type;
    public MoneyDto $price_net;
    public MoneyDto $price_gross;
    public ?string $pickup_eta;
    public ?string $delivery_eta;
    public ?int $min_realistic_time;
}
