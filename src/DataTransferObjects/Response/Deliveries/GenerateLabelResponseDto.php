<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries\GenerateLabelDto;

final class GenerateLabelResponseDto extends AbstractDataTransferObject
{
    public ?GenerateLabelDto $data = null;
}
