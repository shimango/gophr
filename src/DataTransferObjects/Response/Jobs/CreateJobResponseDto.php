<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Jobs\CreateJobDto;

class CreateJobResponseDto extends AbstractDataTransferObject
{
    public ?CreateJobDto $data = null;
}
