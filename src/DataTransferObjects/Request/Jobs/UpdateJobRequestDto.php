<?php

namespace Shimango\Gophr\DataTransferObjects\Request\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class UpdateJobRequestDto extends AbstractDataTransferObject
{
    public ?int $is_confirmed = null;
}
