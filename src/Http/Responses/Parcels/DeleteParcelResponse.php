<?php

namespace Shimango\Gophr\Http\Responses\Parcels;

use Shimango\Gophr\DataTransferObjects\Response\Parcels\DeleteParcelsResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class DeleteParcelResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?DeleteParcelsResponseDto
    {
        return parent::getDataTransferObject(DeleteParcelsResponseDto::class);
    }
}
