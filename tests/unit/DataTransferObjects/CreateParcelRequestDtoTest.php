<?php
namespace Shimango\Gophr\Tests\Unit\DataTransferObjects;

use Shimango\Gophr\DataTransferObjects\Request\Parcels\CreateParcelRequestDto;
use Shimango\Gophr\Tests\Unit\Base\BaseTest;
use Shimango\Gophr\Tests\Unit\Base\Payloads;

class CreateParcelRequestDtoTest extends BaseTest
{
    public function testItIsBuiltFromAParcelPayload(): void
    {
        $parcel = new CreateParcelRequestDto(Payloads::getCreateParcelPayload());

        $this->assertEquals('Big2!', $parcel->parcel_description);
        $this->assertSame(150.0, $parcel->parcel_insurance_value);
    }
}
