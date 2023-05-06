<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Parcels;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class ListParcelsDto extends AbstractDataTransferObject
{
    /** @var \Shimango\Gophr\DataTransferObjects\Items\Response\Parcels\GetParcelDto[] */
    public $parcels = [];
}
