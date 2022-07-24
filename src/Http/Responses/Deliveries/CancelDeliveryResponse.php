<?php

namespace Shimango\Gophr\Http\Responses\Deliveries;

use Shimango\Gophr\DataTransferObjects\Response\Deliveries\CancelDeliveriesResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class CancelDeliveryResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?CancelDeliveriesResponseDto
    {
        return parent::getDataTransferObject(CancelDeliveriesResponseDto::class);
    }
}
