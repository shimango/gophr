<?php

namespace Shimango\Gophr\Http\Responses\Deliveries;

use Shimango\Gophr\DataTransferObjects\Response\Deliveries\CreateDeliveriesResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class CreateDeliveryResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?CreateDeliveriesResponseDto
    {
        return parent::getDataTransferObject(CreateDeliveriesResponseDto::class);
    }
}
