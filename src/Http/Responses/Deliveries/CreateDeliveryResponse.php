<?php

namespace Shimango\Gophr\Http\Responses\Deliveries;

use Shimango\Gophr\DataTransferObjects\Response\Deliveries\CreateDeliveryResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class CreateDeliveryResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?CreateDeliveryResponseDto
    {
        return parent::getDataTransferObject(CreateDeliveryResponseDto::class);
    }
}
