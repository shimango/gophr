<?php

namespace Shimango\Gophr\Http\Responses\Deliveries;

use Shimango\Gophr\DataTransferObjects\Response\Deliveries\GenerateLabelResponseDto;
use Shimango\Gophr\Http\AbstractGophrResponse;

final class GenerateLabelResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?GenerateLabelResponseDto
    {
        return parent::getDataTransferObject(GenerateLabelResponseDto::class);
    }
}
