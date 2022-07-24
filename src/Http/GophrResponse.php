<?php

namespace Shimango\Gophr\Http;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class GophrResponse extends AbstractGophrResponse
{
    public function getContentsObject(): ?AbstractDataTransferObject
    {
        return null;
    }
}
