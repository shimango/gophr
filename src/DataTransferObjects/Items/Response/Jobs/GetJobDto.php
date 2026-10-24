<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Jobs;

final class GetJobDto extends CreateJobDto
{
    /** @var int|string|null */
    public $job_number = null;
}
