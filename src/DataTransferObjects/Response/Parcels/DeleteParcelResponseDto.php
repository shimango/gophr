<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Parcels;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Parcels\DeleteParcelDto;

class DeleteParcelResponseDto extends AbstractDataTransferObject
{
    public ?DeleteParcelDto $data;
}
