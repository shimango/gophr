<?php
namespace Shimango\Gophr\Tests\Unit\Http;

use GuzzleHttp\Psr7\Utils;
use Shimango\Gophr\Http\Responses\Deliveries\GenerateLabelResponse;
use Shimango\Gophr\Http\Responses\Deliveries\GetDeliveryResponse;
use Shimango\Gophr\Http\Responses\Jobs\GetJobResponse;
use Shimango\Gophr\Http\Responses\Jobs\GetQuoteResponse;
use Shimango\Gophr\Http\Responses\Jobs\ListJobsResponse;
use Shimango\Gophr\Http\Responses\Parcels\GetParcelResponse;
use Shimango\Gophr\Tests\Unit\Base\BaseTest;

class ResponseHydrationTest extends BaseTest
{
    private const JOB = '{
        "job_id": "20260812-061700-abc",
        "job_number": 2112,
        "is_confirmed": 1,
        "vehicle_type": 30,
        "price_net": {"amount": 12, "currency": "GBP"},
        "price_gross": {"amount": 14.4, "currency": "GBP"},
        "job_priority": 0,
        "distance": 3.0,
        "a_field_added_later": "ignored",
        "deliveries": [{
            "delivery_id": "20260812-061700-def",
            "status": "BOOKED",
            "leg_type": "STANDARD",
            "private_job_url": "https://app.gophr.com/job",
            "public_tracker_url": null,
            "pickup_sequence_number": 1,
            "dropoff_sequence_number": 2,
            "parcels": [{"parcel_id": "1", "parcel_external_id": "001", "barcode_reference": null}]
        }]
    }';

    public function testJobResponseIsHydrated(): void
    {
        $job = (new GetJobResponse(Utils::streamFor('{"data": ' . self::JOB . '}'), 200, []))->getContentsObject()->data;

        $this->assertEquals('20260812-061700-abc', $job->job_id);
        $this->assertEquals(2112, $job->job_number);
        $this->assertSame(12.0, $job->price_net->amount);
        $this->assertEquals('BOOKED', $job->deliveries[0]->status);
        $this->assertEquals('001', $job->deliveries[0]->parcels[0]->parcel_external_id);
    }

    public function testListJobsResponseIsHydrated(): void
    {
        $body = '{
            "data": {"jobs": [' . self::JOB . ']},
            "links": {"first": "https://api.gophr.com/jobs?page=1", "previous": null, "next": null},
            "meta": {"page": {"current_page": 1, "count": 1, "total": 1}}
        }';

        $jobs = (new ListJobsResponse(Utils::streamFor($body), 200, []))->getContentsObject();

        $this->assertCount(1, $jobs->data->jobs);
        $this->assertEquals(1, $jobs->meta->page->total);
    }

    public function testDeliveryResponseIsHydratedWithHandoverFields(): void
    {
        $body = '{"data": {
            "delivery_id": "20260812-061700-def",
            "status": "NEW",
            "leg_type": "STANDARD",
            "private_job_url": "https://app.gophr.com/job",
            "public_tracker_url": "https://track.gophr.com/abc",
            "pickup": {
                "earliest_pickup_time": "2026-08-12T06:17:00+00:00",
                "pickup_deadline": null,
                "pickup_address1": "221B Baker Street",
                "pickup_city": "London",
                "pickup_postcode": "NW1 6TS",
                "pickup_country_code": "GB",
                "pickup_location_lat": "51.5237",
                "pickup_location_lng": "-0.1585",
                "pickup_person_name": "Sherlock Holmes",
                "pickup_mobile_number": "07700000000",
                "pickup_proof_required": 1,
                "sequence_number": 1
            },
            "dropoff": {
                "min_required_age": 0,
                "dropoff_address1": "10 Downing Street",
                "dropoff_city": "London",
                "dropoff_postcode": "SW1A 2AA",
                "dropoff_country_code": "GB",
                "dropoff_person_name": "The prime minister",
                "dropoff_mobile_number": "07588000000",
                "dropoff_proof_required": 3,
                "cold_chain": 0,
                "sequence_number": 2,
                "fallback_handover_method": "leave_safe",
                "safe_place_location": "reception_desk",
                "leave_safe_instructions": "Leave with the security guard"
            },
            "parcels": [{"parcel_id": "1", "parcel_external_id": "001", "barcode_reference": "GPR001"}]
        }}';

        $delivery = (new GetDeliveryResponse(Utils::streamFor($body), 200, []))->getContentsObject()->data;

        $this->assertEquals('leave_safe', $delivery->dropoff->fallback_handover_method);
        $this->assertEquals('reception_desk', $delivery->dropoff->safe_place_location);
        $this->assertEquals('Leave with the security guard', $delivery->dropoff->leave_safe_instructions);
        $this->assertEquals('221B Baker Street', $delivery->pickup->pickup_address1);
    }

    public function testParcelResponseIsHydratedWithDangerousGoods(): void
    {
        $body = '{"data": {
            "parcel_id": "1",
            "delivery_id": "20260812-061700-def",
            "parcel_external_id": "001",
            "parcel_reference_number": null,
            "parcel_description": "Batteries",
            "parcel_insurance_value": {"amount": null, "currency": "GBP"},
            "barcode_reference": "GPR001",
            "width": 10.0, "length": 15.0, "height": 5.0, "weight": 2,
            "id_check": 0, "is_food": 0, "is_fragile": 0, "is_liquid": 0, "is_not_rotatable": 0,
            "is_glass": 0, "is_baked": 0, "is_flower": 0, "is_alcohol": 0, "is_beef": 0, "is_pork": 0,
            "is_medical": 0,
            "min_required_age": null,
            "dangerous_goods": "BATTERY_LITHIUM_ION_STANDALONE"
        }}';

        $parcel = (new GetParcelResponse(Utils::streamFor($body), 200, []))->getContentsObject()->data;

        $this->assertEquals('BATTERY_LITHIUM_ION_STANDALONE', $parcel->dangerous_goods);
        $this->assertSame(2.0, $parcel->weight);
        $this->assertNull($parcel->parcel_insurance_value->amount);
    }

    public function testQuoteResponseIsHydrated(): void
    {
        $body = '{"data": {
            "job_priority": 0,
            "vehicle_type": 10,
            "price_net": {"amount": 9.5, "currency": "GBP"},
            "price_gross": {"amount": 11.4, "currency": "GBP"},
            "distance": 4.2,
            "pickup_eta": "2026-08-12T06:30:00+00:00",
            "delivery_eta": "2026-08-12T07:10:00+00:00",
            "min_realistic_time": 40.0,
            "average_delivery_time": 35
        }}';

        $quote = (new GetQuoteResponse(Utils::streamFor($body), 200, []))->getContentsObject()->data;

        $this->assertSame(40.0, $quote->min_realistic_time);
        $this->assertEquals(35, $quote->average_delivery_time);
        $this->assertSame(9.5, $quote->price_net->amount);
    }

    public function testErrorResponseHasNoData(): void
    {
        $body = '{"errors": [{"object": null, "code": "NOT_FOUND", "message": "Not Found"}]}';

        $response = new GetJobResponse(Utils::streamFor($body), 404, []);

        $this->assertNull($response->getContentsObject()->data);
        $this->assertEquals('NOT_FOUND', $response->getContentsArray()['errors'][0]['code']);
    }

    public function testEmptyOrInvalidBodyDoesNotThrow(): void
    {
        $empty = new GetJobResponse(Utils::streamFor(''), 0, []);
        $html = new GetJobResponse(Utils::streamFor('<html>502 Bad Gateway</html>'), 502, []);

        $this->assertSame([], $empty->getContentsArray());
        $this->assertNull($html->getContentsObject()->data);
    }

    public function testLabelResponseIsHydrated(): void
    {
        $body = '{"data": {"url": "https://storage.gophr.com/labels/abc.pdf"}}';

        $label = (new GenerateLabelResponse(Utils::streamFor($body), 200, []))->getContentsObject()->data;

        $this->assertEquals('https://storage.gophr.com/labels/abc.pdf', $label->url);
    }
}
