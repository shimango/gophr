<?php

namespace Shimango\Gophr\DataTransferObjects\Request\Parcels;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class UpdateParcelRequestDto extends AbstractDataTransferObject
{
    public ?string $parcel_external_id;
    public ?string $parcel_reference_number;
    public ?string $parcel_description;
    public ?float $parcel_insurance_value;
    public ?float $width;
    public ?float $length;
    public ?float $height;
    public ?float $weight;
    public ?int $id_check;
    public ?int $is_food;
    public ?int $is_fragile;
    public ?int $is_liquid;
    public ?int $is_not_rotatable;
    public ?int $is_glass;
    public ?int $is_baked;
    public ?int $is_flower;
    public ?int $is_alcohol;
    public ?int $is_beef;
    public ?int $is_pork;
}
