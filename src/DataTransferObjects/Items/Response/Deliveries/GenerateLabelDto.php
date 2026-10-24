<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Deliveries;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

final class GenerateLabelDto extends AbstractDataTransferObject
{
    public ?string $url = null;
}
