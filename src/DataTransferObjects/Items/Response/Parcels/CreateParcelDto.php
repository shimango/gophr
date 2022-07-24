<?php

namespace Shimango\Gophr\DataTransferObjects\Items\Response\Parcels;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\DataTransferObjects\Items\MoneyDto;

class CreateParcelDto extends AbstractDataTransferObject
{
    public string $parcel_id;
    public string $delivery_id;
    public ?string $parcel_external_id;
    public ?string $parcel_reference_number;
    public ?string $parcel_description;
    public MoneyDto $parcel_insurance_value;
    public ?string $barcode_reference;
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
