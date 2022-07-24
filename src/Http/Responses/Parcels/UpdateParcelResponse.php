<?php

namespace Shimango\Gophr\Http\Responses\Parcels;

use Shimango\Gophr\DataTransferObjects\Response\Parcels\UpdateParcelsResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class UpdateParcelResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?UpdateParcelsResponseDto
    {
        return parent::getDataTransferObject(UpdateParcelsResponseDto::class);
    }
}
