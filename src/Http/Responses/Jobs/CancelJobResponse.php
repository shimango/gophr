<?php

namespace Shimango\Gophr\Http\Responses\Jobs;

use Shimango\Gophr\DataTransferObjects\Response\Jobs\CancelJobResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

class CancelJobResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?CancelJobResponseDto
    {
        return parent::getDataTransferObject(CancelJobResponseDto::class);
    }
}
