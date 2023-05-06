<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Jobs\GetJobDto;

class GetJobResponseDto extends AbstractDataTransferObject
{
    public ?GetJobDto $data = null;
}
