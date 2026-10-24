<?php

namespace Shimango\Gophr\DataTransferObjects;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Shimango\Gophr\Interfaces\DataTransferObjectInterface;
use Spatie\DataTransferObject\DataTransferObject;

abstract class AbstractDataTransferObject extends DataTransferObject implements DataTransferObjectInterface
{
    public function __construct(array $parameters = [])
    {
        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $type = $property->getType();
            $name = $property->getName();

            if ($type instanceof ReflectionNamedType === false || $type->getName() !== 'float') {
                continue;
            }

            if (is_int($parameters[$name] ?? null) === true) {
                $parameters[$name] = (float)$parameters[$name];
            }
        }

        parent::__construct($parameters);
    }

    public function toArray(): array
    {
        return array_filter(parent::toArray());
    }
}
