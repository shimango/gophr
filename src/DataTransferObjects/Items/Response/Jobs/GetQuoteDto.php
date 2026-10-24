<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\MoneyDto;

final class GetQuoteDto extends AbstractDataTransferObject
{
    public ?int $job_priority = null;

    public ?int $vehicle_type = null;

    public ?MoneyDto $price_net = null;

    public ?MoneyDto $price_gross = null;

    public ?string $pickup_eta = null;

    public ?string $delivery_eta = null;

    public ?float $min_realistic_time = null;

    public ?int $average_delivery_time = null;

    public ?float $distance = null;
}
