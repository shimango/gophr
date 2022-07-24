<?php

namespace Shimango\Gophr\Http\Responses\Parcels;

use Shimango\Gophr\DataTransferObjects\Response\Parcels\GetParcelsResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class GetParcelResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?GetParcelsResponseDto
    {
        return parent::getDataTransferObject(GetParcelsResponseDto::class);
    }
}
