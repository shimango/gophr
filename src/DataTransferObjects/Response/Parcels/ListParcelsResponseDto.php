<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Parcels;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Parcels\ListParcelsDto;

class ListParcelsResponseDto extends AbstractDataTransferObject
{
    public ?ListParcelsDto $data = null;
}
