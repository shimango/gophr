<?php

namespace Shimango\Gophr\DataTransferObjects\Pickups;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class PickupDto extends AbstractDataTransferObject
{
    public ?string $earliest_pickup_time;
    public ?string $pickup_deadline;
    public ?string $pickup_address1;
    public ?string $pickup_address2;
    public ?string $pickup_city;
    public ?string $pickup_postcode;
    public ?string $pickup_country_code;
    public ?string $pickup_w3w;
    public ?string $pickup_location_lat;
    public ?string $pickup_location_lng;
    public ?string $pickup_company_name;
    public ?string $pickup_tips_how_to_find;
    public ?string $pickup_person_name;
    public ?string $pickup_email;
    public ?string $pickup_mobile_number;
    public ?string $pickup_phone_number;
    public ?string $pickup_instructions;
    public ?int $pickup_proof_required;
    public ?int $sequence_number;
}
