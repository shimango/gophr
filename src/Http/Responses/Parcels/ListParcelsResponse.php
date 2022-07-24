<?php

namespace Shimango\Gophr\Http\Responses\Parcels;

use Shimango\Gophr\DataTransferObjects\Response\Parcels\ListParcelsResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class ListParcelsResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?ListParcelsResponseDto
    {
        return parent::getDataTransferObject(ListParcelsResponseDto::class);
    }
}
