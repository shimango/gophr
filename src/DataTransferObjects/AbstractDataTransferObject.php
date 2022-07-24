<?php

namespace Shimango\Gophr\DataTransferObjects;

use Shimango\Gophr\Interfaces\DataTransferObjectInterface;
use Spatie\DataTransferObject\DataTransferObject;

class AbstractDataTransferObject extends DataTransferObject implements DataTransferObjectInterface
{
    private array $floatFields = [
        'amount', 'distance'
    ];

    public function __construct(array $parameters = [])
    {
        foreach ($this->floatFields as $floatField) {
            if (isset($parameters[$floatField]) && is_int($parameters[$floatField])) {
                $parameters[$floatField] = (float)$parameters[$floatField];
            }
        }

        parent::__construct($parameters);
    }
}
