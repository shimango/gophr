<?php

namespace Shimango\Gophr\Http\Responses\Deliveries;

use Shimango\Gophr\DataTransferObjects\Response\Deliveries\UpdateDeliveriesResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class UpdateDeliveryResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?UpdateDeliveriesResponseDto
    {
        return parent::getDataTransferObject(UpdateDeliveriesResponseDto::class);
    }
}
