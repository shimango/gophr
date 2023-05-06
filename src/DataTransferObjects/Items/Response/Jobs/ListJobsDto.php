<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class ListJobsDto extends AbstractDataTransferObject
{
    /** @var \Shimango\Gophr\DataTransferObjects\Items\Response\Jobs\GetJobDto[] */
    public $jobs = [];
}
