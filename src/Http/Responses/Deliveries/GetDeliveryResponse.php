<?php

namespace Shimango\Gophr\Http\Responses\Deliveries;

use Shimango\Gophr\DataTransferObjects\Response\Deliveries\GetDeliveriesResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class GetDeliveryResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?GetDeliveriesResponseDto
    {
        return parent::getDataTransferObject(GetDeliveriesResponseDto::class);
    }
}
