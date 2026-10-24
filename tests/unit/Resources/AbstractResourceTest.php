<?php
namespace Shimango\Gophr\Tests\Unit\Resources;

use Shimango\Gophr\Http\GophrRequest;
use Shimango\Gophr\Http\Responses\Jobs\CreateJobResponse;
use Shimango\Gophr\Resources\Job;
use Shimango\Gophr\Tests\Unit\Base\BaseTest;

class AbstractResourceTest extends BaseTest
{
    public function testCreateAddsBookingMethodAsAKeyValuePair(): void
    {
        $body = ['meta_data' => [['key' => 'order', 'value' => '2112']]];

        $expectedBody = ['meta_data' => [
            ['key' => 'order', 'value' => '2112'],
            ['key' => 'booking_method', 'value' => 'shimango'],
        ]];

        $this->assertBodySent($body, $expectedBody);
    }

    public function testCreateAddsBookingMethodWhenThereIsNoMetaData(): void
    {
        $expectedBody = ['meta_data' => [['key' => 'booking_method', 'value' => 'shimango']]];

        $this->assertBodySent([], $expectedBody);
    }

    public function testCreateKeepsAnExistingBookingMethod(): void
    {
        $body = ['meta_data' => [['key' => 'booking_method', 'value' => 'my-platform']]];

        $this->assertBodySent($body, $body);
    }

    private function assertBodySent(array $body, array $expectedBody): void
    {
        $gophrRequest = $this->createMock(GophrRequest::class);
        $gophrRequest->method('setEndpoint')->willReturnSelf();
        $gophrRequest->method('setReturnType')->willReturnSelf();
        $gophrRequest->method('execute')->willReturn($this->createMock(CreateJobResponse::class));
        $gophrRequest->expects($this->once())->method('attachBody')->with($expectedBody)->willReturnSelf();

        (new Job($gophrRequest))->create('/jobs', $body, CreateJobResponse::class);
    }
}
