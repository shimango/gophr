<?php

namespace Shimango\Gophr\DataTransferObjects\Dropoffs;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;

class DropoffDto extends AbstractDataTransferObject
{
    public ?int $min_required_age;
    public ?string $dropoff_company_name;
    public ?string $dropoff_address1;
    public ?string $dropoff_address2;
    public ?string $dropoff_city;
    public ?string $dropoff_postcode;
    public ?string $dropoff_country_code;
    public ?string $dropoff_w3w;
    public ?string $dropoff_location_lat;
    public ?string $dropoff_location_lng;
    public ?string $dropoff_tips_how_to_find;
    public ?string $dropoff_person_name;
    public ?string $dropoff_email;
    public ?string $dropoff_mobile_number;
    public ?string $dropoff_phone_number;
    public ?string $dropoff_instructions;
    public ?string $earliest_dropoff_time;
    public ?string $dropoff_deadline;
    public ?int $dropoff_proof_required;
    public ?int $cold_chain;
    public ?int $sequence_number;
}
