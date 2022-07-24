<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Parcels;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Parcels\DeleteParcelDto;

class DeleteParcelsResponseDto extends AbstractDataTransferObject
{
    public ?DeleteParcelDto $data;
}
