<?php

namespace Shimango\Gophr\DataTransferObjects\Response\Jobs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\Response\Jobs\GetQuoteDto;

class GetQuoteResponseDto extends AbstractDataTransferObject
{
    public ?GetQuoteDto $data;
}
