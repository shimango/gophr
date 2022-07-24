<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Jobs\UpdateJobDto;

class UpdateJobResponseDto extends AbstractDataTransferObject
{
    public ?UpdateJobDto $data;
}
