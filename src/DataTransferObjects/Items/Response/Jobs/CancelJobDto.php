<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\MoneyDto;

class CancelJobDto extends AbstractDataTransferObject
{
    public ?MoneyDto $cancelation_cost = null;
}
