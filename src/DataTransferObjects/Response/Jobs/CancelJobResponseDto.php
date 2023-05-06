<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Jobs\CancelJobDto;

class CancelJobResponseDto extends AbstractDataTransferObject
{
    public ?CancelJobDto $data = null;
}
