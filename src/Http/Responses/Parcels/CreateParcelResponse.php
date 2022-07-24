<?php

namespace Shimango\Gophr\Http\Responses\Parcels;

use Shimango\Gophr\DataTransferObjects\Response\Parcels\CreateParcelsResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class CreateParcelResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?CreateParcelsResponseDto
    {
        return parent::getDataTransferObject(CreateParcelsResponseDto::class);
    }
}
