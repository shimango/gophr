<?php

namespace Shimango\Gophr\DataTransferObjects\Items;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class Links extends AbstractDataTransferObject
{
    public string $first;
    public ?string $previous;
    public ?string $next;
}
