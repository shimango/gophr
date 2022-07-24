<?php

namespace Shimango\Gophr\Http\Responses\Jobs;

use Shimango\Gophr\DataTransferObjects\Response\Jobs\ListJobsResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class ListJobsResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?ListJobsResponseDto
    {
        return parent::getDataTransferObject(ListJobsResponseDto::class);
    }
}
