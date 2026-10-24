# Gophr PHP Client

A PHP client for the [Gophr Commercial API V2](https://developers.gophr.com/docs/gophr-commercial-api). Use it to get quotes, book same-day courier deliveries, manage deliveries and parcels, and print labels from your PHP application.

[![Build Status](https://github.com/shimango/gophr/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/shimango/gophr/actions/workflows/tests.yml?query=branch%3Amaster)
[![Latest Stable Version](https://poser.pugx.org/shimango/gophr/v)](https://packagist.org/packages/shimango/gophr)
[![Total Downloads](https://poser.pugx.org/shimango/gophr/downloads)](https://packagist.org/packages/shimango/gophr)
[![License](https://poser.pugx.org/shimango/gophr/license)](https://packagist.org/packages/shimango/gophr)
[![PHP Version Require](https://poser.pugx.org/shimango/gophr/require/php)](https://packagist.org/packages/shimango/gophr)

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [How the API Is Organised](#how-the-api-is-organised)
- [Jobs](#jobs)
- [Deliveries](#deliveries)
- [Parcels](#parcels)
- [Field Reference](#field-reference)
- [Reading Responses](#reading-responses)
- [Handling Errors](#handling-errors)
- [Receiving Webhooks](#receiving-webhooks)
- [Testing Your Integration](#testing-your-integration)
- [Method Reference](#method-reference)
- [Development](#development)

## Requirements

- PHP 8.0 or higher
- [Composer](https://getcomposer.org/)
- A Gophr API key. See [Authentication](https://developers.gophr.com/docs/authorisation) to create one. Sandbox and production use different keys.

## Installation

```bash
composer require shimango/gophr
```

Then load Composer's autoloader:

```php
require_once 'vendor/autoload.php';
```

## Quick Start

Get a price for a delivery, then book it.

```php
<?php
require_once 'vendor/autoload.php';

use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;

$gophr = new Client(new Configuration('YOUR_SANDBOX_API_KEY', true));

$tomorrow = new DateTimeImmutable('tomorrow 09:00');

$job = [
    'external_id' => 'ORDER-1001',
    'pickups' => [[
        'earliest_pickup_time' => $tomorrow->format(DATE_ATOM),
        'pickup_deadline' => $tomorrow->modify('+1 hour')->format(DATE_ATOM),
        'pickup_address1' => '221B Baker Street',
        'pickup_city' => 'London',
        'pickup_postcode' => 'NW1 6XE',
        'pickup_country_code' => 'GB',
        'pickup_person_name' => 'Sherlock Holmes',
        'pickup_mobile_number' => '07700900001',
        'parcels' => [[
            'parcel_external_id' => 'P1',
            'parcel_description' => 'Box of books',
            'width' => 30,   // cm
            'length' => 40,  // cm
            'height' => 20,  // cm
            'weight' => 4.5, // kg
        ]],
    ]],
    'dropoffs' => [[
        'dropoff_address1' => '10 Downing Street',
        'dropoff_city' => 'London',
        'dropoff_postcode' => 'SW1A 2AA',
        'dropoff_country_code' => 'GB',
        'dropoff_person_name' => 'John Watson',
        'dropoff_mobile_number' => '07700900002',
        'dropoff_deadline' => $tomorrow->modify('+3 hours')->format(DATE_ATOM),
        'parcels' => [['parcel_external_id' => 'P1']],
    ]],
];

// 1. Get a quote
$quote = $gophr->getQuote($job)->getContentsObject()->data;
echo "Price: {$quote->price_gross->amount} {$quote->price_gross->currency}\n";

// 2. Book and confirm the job
$response = $gophr->createJob(array_merge($job, ['is_confirmed' => 1]));

if ($response->getStatusCode() !== 201) {
    print_r($response->getContentsArray()['errors']);
    exit(1);
}

$booked = $response->getContentsObject()->data;
echo "Booked job {$booked->job_id}\n";
echo "Track it at {$booked->deliveries[0]->public_tracker_url}\n";
```

## Configuration

```php
use Shimango\Gophr\Common\Configuration;

$config = new Configuration(
    'YOUR_API_KEY', // API key
    false,          // Use the sandbox (default: false)
    'v2',           // API version, only "v2" is supported (default: "v2")
    null,           // Port of a proxy running on localhost (default: null)
    false           // Verify SSL when using the proxy (default: false)
);
```

| Environment | How to configure | Base URL |
| ----------- | ---------------- | -------- |
| Production | `new Configuration($apiKey)` | `https://api.gophr.com/v2-commercial-api` |
| Sandbox | `new Configuration($apiKey, true)` | `https://api-sandbox.gophr.com/v2-commercial-api` |

In the sandbox, booked jobs are never sent to real couriers. Use it while you build and test your integration.

To send all requests through a local proxy (for example to inspect traffic), pass its port. Requests then go to `http://localhost:<port>`:

```php
$config = new Configuration('YOUR_API_KEY', true, 'v2', 8080, true);
```

The client also reads two environment variables, which are useful when developing against a local copy of the API:

| Variable | Effect |
| -------- | ------ |
| `REST_BASE_URL_DEVELOPMENT` | Replaces the base URL, e.g. `http://localhost:8000` |
| `GOPHR_XDEBUG_SESSION` | Adds an `XDEBUG_SESSION` query parameter to every request |

`$gophr->getBaseApiEndpoint()` returns the full base URL the client is using.

## How the API Is Organised

- A **job** is a booking. It has one pickup and one or more dropoffs, and is priced as a whole.
- A **delivery** is one pickup and dropoff pair within a job. A job with three dropoffs has three deliveries.
- A **parcel** is a physical item. Parcels are declared at the pickup, and each dropoff lists which parcels it receives by their `parcel_external_id`.

Every job has a single pickup. Pickup and delivery must happen on the same day.

### Draft and Confirmed Jobs

A job is either a **draft** (`is_confirmed: 0`) or **confirmed** (`is_confirmed: 1`). Only confirmed jobs are sent to couriers. You can create a confirmed job in one call, or create a draft, edit it and confirm it later (see [Building a Job Step by Step](#building-a-job-step-by-step)).

| Action | Draft | Confirmed |
| ------ | :---: | :-------: |
| Add or cancel a delivery | Yes | No |
| Add, update or delete a parcel | Yes | No |
| Update delivery addresses and times | Yes | No |
| Update delivery contacts and instructions | Yes | Yes, until the courier reaches the stop |
| Set or change handover options and `dangerous_goods` | Yes | No |
| Cancel the job | Yes | Yes |

### IDs

The API generates a `job_id`, a `delivery_id` for each delivery and a `parcel_id` for each parcel. Store them, because every other call needs them. You choose `external_id` (your reference for the job) and `parcel_external_id` (your reference for each parcel, unique within the job).

## Jobs

### Creating a Job

This example books a confirmed job with two parcels and uses several of the optional features described in the [Field Reference](#field-reference).

```php
$tomorrow = new DateTimeImmutable('tomorrow 09:00');

$jobData = [
    'external_id' => 'ORDER-1002',
    'is_confirmed' => 1,
    'vehicle_type' => 30, // car
    'pickups' => [[
        'earliest_pickup_time' => $tomorrow->format(DATE_ATOM),
        'pickup_deadline' => $tomorrow->modify('+1 hour')->format(DATE_ATOM),
        'pickup_address1' => '221B Baker Street',
        'pickup_city' => 'London',
        'pickup_postcode' => 'NW1 6XE',
        'pickup_country_code' => 'GB',
        'pickup_company_name' => 'Elementary Investigations',
        'pickup_person_name' => 'Sherlock Holmes',
        'pickup_email' => 'sherlock@example.com',
        'pickup_mobile_number' => '07700900001',
        'pickup_instructions' => 'Ring the bell for flat B',
        'parcels' => [
            [
                'parcel_external_id' => 'P1',
                'parcel_reference_number' => 'ORDER-1002-1',
                'parcel_description' => 'Violin',
                'width' => 25,
                'length' => 80,
                'height' => 15,
                'weight' => 2.5,
                'is_fragile' => 1,
                'is_not_rotatable' => 1,
            ],
            [
                'parcel_external_id' => 'P2',
                'parcel_reference_number' => 'ORDER-1002-2',
                'parcel_description' => 'Folding chairs',
                'width' => 50,
                'length' => 80,
                'height' => 5,
                'weight' => 6, // total weight of all 4 chairs
                'quantity' => 4,
            ],
        ],
    ]],
    'dropoffs' => [[
        'dropoff_company_name' => 'Scotland Yard',
        'dropoff_address1' => '10 Downing Street',
        'dropoff_city' => 'London',
        'dropoff_postcode' => 'SW1A 2AA',
        'dropoff_country_code' => 'GB',
        'dropoff_person_name' => 'John Watson',
        'dropoff_email' => 'watson@example.com',
        'dropoff_mobile_number' => '07700900002',
        'dropoff_instructions' => 'Deliver to the front desk',
        'dropoff_deadline' => $tomorrow->modify('+3 hours')->format(DATE_ATOM),
        'dropoff_proof_required' => 1, // photo
        'pin_required' => 1,
        'fallback_handover_method' => 'leave_safe',
        'safe_place_location' => 'reception_desk',
        'leave_safe_instructions' => 'Leave with the security desk',
        'parcels' => [
            ['parcel_external_id' => 'P1'],
            ['parcel_external_id' => 'P2'],
        ],
    ]],
    'meta_data' => [
        ['key' => 'order_number', 'value' => 'ORDER-1002'],
    ],
];

$response = $gophr->createJob($jobData);
$job = $response->getContentsObject()->data;

echo "Job {$job->job_id} costs {$job->price_gross->amount} {$job->price_gross->currency}\n";

foreach ($job->deliveries as $delivery) {
    echo "Delivery {$delivery->delivery_id} is {$delivery->status}\n";
}
```

The response is a summary. Each delivery includes its `status`, tracking URLs and parcel IDs. Use [`getDelivery()`](#getting-a-delivery) for the full pickup and dropoff details.

### Getting a Quote

`getQuote()` takes the same payload as `createJob()` and returns the price and ETAs without booking anything. Contact names and mobile numbers are optional for a quote.

```php
$quote = $gophr->getQuote($jobData)->getContentsObject()->data;

echo "Price: {$quote->price_gross->amount} {$quote->price_gross->currency}\n";
echo "Vehicle type: {$quote->vehicle_type}\n";
echo "Pickup ETA: {$quote->pickup_eta}\n";
echo "Delivery ETA: {$quote->delivery_eta}\n";
echo "Minimum delivery time: {$quote->min_realistic_time} minutes\n";
```

### Multi-Drop Jobs

Add more dropoffs to deliver to several places in one job. Gophr optimises the order of the stops unless you set `is_fixed_sequence` to `1`, in which case `sequence_number` is required and the courier follows your order. The pickup is always sequence `1`.

```php
$tomorrow = new DateTimeImmutable('tomorrow 09:00');

$pickup = [
    'sequence_number' => 1,
    'earliest_pickup_time' => $tomorrow->format(DATE_ATOM),
    'pickup_deadline' => $tomorrow->modify('+1 hour')->format(DATE_ATOM),
    'pickup_address1' => '221B Baker Street',
    'pickup_city' => 'London',
    'pickup_postcode' => 'NW1 6XE',
    'pickup_country_code' => 'GB',
    'pickup_person_name' => 'Sherlock Holmes',
    'pickup_mobile_number' => '07700900001',
    'parcels' => [
        ['parcel_external_id' => 'A', 'width' => 20, 'length' => 30, 'height' => 10, 'weight' => 1],
        ['parcel_external_id' => 'B', 'width' => 20, 'length' => 30, 'height' => 10, 'weight' => 1],
    ],
];

$dropoff = fn (int $sequence, string $address, string $postcode, string $parcel): array => [
    'sequence_number' => $sequence,
    'dropoff_address1' => $address,
    'dropoff_city' => 'London',
    'dropoff_postcode' => $postcode,
    'dropoff_country_code' => 'GB',
    'dropoff_person_name' => 'Recipient',
    'dropoff_mobile_number' => '07700900002',
    'dropoff_deadline' => $tomorrow->modify('+4 hours')->format(DATE_ATOM),
    'parcels' => [['parcel_external_id' => $parcel]],
];

$response = $gophr->createJob([
    'external_id' => 'ROUND-7',
    'is_confirmed' => 1,
    'is_fixed_sequence' => 1,
    'pickups' => [$pickup],
    'dropoffs' => [
        $dropoff(2, '10 Downing Street', 'SW1A 2AA', 'A'),
        $dropoff(3, 'Buckingham Palace', 'SW1A 1AA', 'B'),
    ],
]);
```

### Building a Job Step by Step

Create a draft, adjust it, check the price, then confirm it.

```php
// 1. Create a draft (a job is a draft unless is_confirmed is 1)
$draft = $gophr->createJob(array_merge($jobData, ['is_confirmed' => 0]))->getContentsObject()->data;
$jobId = $draft->job_id;

// 2. Add another delivery (see "Adding a Delivery" below for $deliveryData)
$gophr->createDelivery($jobId, $deliveryData);

// 3. Remove a parcel that is no longer needed
$delivery = $draft->deliveries[0];
$gophr->deleteParcel($jobId, $delivery->delivery_id, $delivery->parcels[0]->parcel_id);

// 4. Check the updated price
$job = $gophr->getJob($jobId)->getContentsObject()->data;
echo "New price: {$job->price_gross->amount}\n";

// 5. Confirm the job to send it to couriers
$gophr->updateJob($jobId, ['is_confirmed' => 1]);
```

### Getting a Job

```php
$job = $gophr->getJob('20260812-061700-8c020adae3835f84')->getContentsObject()->data;

echo "Confirmed: {$job->is_confirmed}\n";
echo "Distance: {$job->distance}\n";
```

### Confirming a Job

`updateJob()` only confirms a draft. `is_confirmed` must be `1`.

```php
$job = $gophr->updateJob($jobId, ['is_confirmed' => 1])->getContentsObject()->data;
```

### Listing Jobs

`listJobs($page, $count, $includeFinished)` returns your jobs one page at a time. `$count` is between 1 and 200 (default 15). Finished jobs are left out unless `$includeFinished` is `true`.

```php
$page = 1;

do {
    $response = $gophr->listJobs($page, 50, true)->getContentsObject();

    foreach ($response->data->jobs as $job) {
        echo "{$job->job_id}: {$job->price_gross->amount}\n";
    }

    $page++;
} while ($response->links->next !== null);

echo "Total jobs: {$response->meta->page->total}\n";
```

### Cancelling a Job

You can cancel a job at any time, but a fee may apply once it is confirmed. See [Cancelling a Job](https://developers.gophr.com/docs/cancelling-a-job) for the fees. The response tells you what was charged.

```php
$response = $gophr->cancelJob($jobId, [
    'cancelled_reason' => 'ORDER_CANCELLED',
    'cancelled_comment' => 'The customer cancelled their order',
]);

$cost = $response->getContentsObject()->data->cancelation_cost;
echo "Cancellation cost: {$cost->amount} {$cost->currency}\n";
```

`cancelled_reason` is one of `ORDER_CANCELLED`, `WRONG_INFO`, `COURIER_LATE`, `DUPLICATED`, `FRAUDULENT_ORDER`, `NOT_ACCEPTED`, `NRT`, `RESCHEDULED`, `TECHNICAL_ISSUES`, `TEST_ORDER` or `NO_NEED`. Both fields are optional.

## Deliveries

### Adding a Delivery

Adds a pickup and dropoff pair to a draft job. Because a job has a single pickup, `pickup_address1`, `pickup_postcode`, `pickup_country_code`, `pickup_person_name` and `pickup_mobile_number` must match the job's existing pickup. Note that here `pickup` and `dropoff` are single objects and `parcels` sits beside them.

```php
$tomorrow = new DateTimeImmutable('tomorrow 09:00');

$deliveryData = [
    'pickup' => [
        'earliest_pickup_time' => $tomorrow->format(DATE_ATOM),
        'pickup_deadline' => $tomorrow->modify('+1 hour')->format(DATE_ATOM),
        'pickup_address1' => '221B Baker Street',
        'pickup_city' => 'London',
        'pickup_postcode' => 'NW1 6XE',
        'pickup_country_code' => 'GB',
        'pickup_person_name' => 'Sherlock Holmes',
        'pickup_mobile_number' => '07700900001',
    ],
    'dropoff' => [
        'dropoff_address1' => 'Buckingham Palace',
        'dropoff_city' => 'London',
        'dropoff_postcode' => 'SW1A 1AA',
        'dropoff_country_code' => 'GB',
        'dropoff_person_name' => 'Mrs Hudson',
        'dropoff_mobile_number' => '07700900003',
        'dropoff_deadline' => $tomorrow->modify('+3 hours')->format(DATE_ATOM),
    ],
    'parcels' => [
        [
            'parcel_external_id' => 'P3',
            'parcel_description' => 'Tea set',
            'width' => 30,
            'length' => 30,
            'height' => 20,
            'weight' => 3,
            'is_fragile' => 1,
        ],
    ],
];

$delivery = $gophr->createDelivery($jobId, $deliveryData)->getContentsObject()->data;
echo "Added delivery {$delivery->delivery_id}\n";
```

### Getting a Delivery

Returns the delivery with its full pickup and dropoff details.

```php
$delivery = $gophr->getDelivery($jobId, $deliveryId)->getContentsObject()->data;

echo "Status: {$delivery->status}\n";
echo "Recipient: {$delivery->dropoff->dropoff_person_name}\n";
echo "Deliver by: {$delivery->dropoff->dropoff_deadline}\n";
echo "Tracking: {$delivery->public_tracker_url}\n";
```

### Listing Deliveries

```php
$deliveries = $gophr->listDeliveries($jobId)->getContentsObject()->data->deliveries;

foreach ($deliveries as $delivery) {
    echo "{$delivery->delivery_id}: {$delivery->status} to {$delivery->dropoff->dropoff_postcode}\n";
}
```

### Updating a Delivery

Send only the fields you want to change, inside `pickup` and/or `dropoff`. While the job is a draft, any field can be changed. After confirmation, only these fields can be changed (with the `pickup_` or `dropoff_` prefix):

`instructions`, `tips_how_to_find`, `person_name`, `company_name`, `mobile_number`, `phone_number`, `email`

Pickup fields are locked once the courier is at the pickup, and nothing can be changed once the courier is at the dropoff. Pickup changes apply to every delivery in the job, because they share the pickup.

```php
$gophr->updateDelivery($jobId, $deliveryId, [
    'dropoff' => [
        'dropoff_phone_number' => '02079460000',
        'dropoff_instructions' => 'Use the side entrance',
    ],
]);
```

Parcels are not changed through this method. Use the [parcel methods](#parcels) instead.

### Cancelling a Delivery

Removes one delivery from a draft job. If it is the job's only delivery, the whole job is cancelled. Once a job is confirmed, cancel the job instead. The fields are the same as for [cancelling a job](#cancelling-a-job).

```php
$gophr->cancelDelivery($jobId, $deliveryId, [
    'cancelled_reason' => 'WRONG_INFO',
    'cancelled_comment' => 'Incorrect address',
]);
```

### Printing a Label

Generates a printable label for a delivery and returns a link to download it. `label_size` is optional and can be `small` or `regular`.

```php
$label = $gophr->generateLabel($jobId, $deliveryId, ['label_size' => 'small'])->getContentsObject()->data;

file_put_contents("label-{$deliveryId}.pdf", file_get_contents($label->url));
```

## Parcels

Parcels can only be added, updated or deleted while the job is a draft. Reading them works at any time.

### Adding a Parcel

```php
$parcel = $gophr->createParcel($jobId, $deliveryId, [
    'parcel_external_id' => 'P4',
    'parcel_description' => 'Magnifying glass',
    'width' => 15,
    'length' => 25,
    'height' => 5,
    'weight' => 0.4,
    'is_glass' => 1,
])->getContentsObject()->data;

echo "Added parcel {$parcel->parcel_id}\n";
```

### Getting a Parcel

```php
$parcel = $gophr->getParcel($jobId, $deliveryId, $parcelId)->getContentsObject()->data;

echo "{$parcel->parcel_description}: {$parcel->weight} kg\n";
echo "Barcode: {$parcel->barcode_reference}\n";
```

### Listing Parcels

```php
$parcels = $gophr->listParcels($jobId, $deliveryId)->getContentsObject()->data->parcels;

foreach ($parcels as $parcel) {
    echo "{$parcel->parcel_id}: {$parcel->parcel_description} ({$parcel->weight} kg)\n";
}
```

### Updating a Parcel

Send only the fields you want to change.

```php
$gophr->updateParcel($jobId, $deliveryId, $parcelId, [
    'weight' => 3.0,
    'is_not_rotatable' => 1,
]);
```

### Deleting a Parcel

```php
$gophr->deleteParcel($jobId, $deliveryId, $parcelId);
```

## Field Reference

This section covers the most common fields. The [API reference](https://developers.gophr.com/reference) lists every field.

### Required Fields

| Object | Required fields |
| ------ | --------------- |
| Job | `external_id`, `pickups`, `dropoffs` |
| Pickup | `pickup_address1`, `pickup_city`, `pickup_postcode`, `pickup_country_code`, `pickup_person_name`, `pickup_mobile_number`, `parcels` |
| Dropoff | `dropoff_address1`, `dropoff_city`, `dropoff_postcode`, `dropoff_country_code`, `dropoff_person_name`, `dropoff_mobile_number`, `parcels` |
| Parcel | `parcel_external_id`, `width`, `length`, `height`, `weight` |

Parcel dimensions are in centimetres and weight in kilograms. `parcel_description` can be up to 36 characters and `parcel_reference_number` up to 64.

### Times

All times use ISO 8601 with a timezone, e.g. `2026-08-12T09:00:00+01:00`. In PHP, use `$date->format(DATE_ATOM)`.

| Field | Meaning |
| ----- | ------- |
| `earliest_pickup_time` | The goods are ready from this time |
| `pickup_deadline` | Collect by this time. Must be at least 15 minutes in the future |
| `earliest_dropoff_time` | Do not deliver before this time |
| `dropoff_deadline` | Deliver by this time |

Each window must be at least 15 minutes long, and `pickup_deadline` must be at least 15 minutes before `dropoff_deadline`.

### Allowed Values

| Field | Values |
| ----- | ------ |
| `vehicle_type` | `10` pushbike, `15` cargo bike, `20` motorcycle, `30` car, `40` small van, `50` large van |
| `job_priority` | `0` standard, `1` high, `2` rush, `3` direct |
| `pickup_proof_required`, `dropoff_proof_required` | `0` none, `1` photo, `2` signature, `3` photo and signature |
| `fallback_handover_method` | `leave_safe`, `return_to_sender` |
| `safe_place_location` | `mail_box`, `reception_desk`, `security_office`, `loading_bay`, `post_room`, `concierge`, `other` |
| `label_size` | `small`, `regular` |

Boolean flags such as `is_confirmed`, `is_fragile` and `pin_required` take `0` or `1`.

`vehicle_type` is a preference. If the parcels do not fit the chosen vehicle, Gophr assigns a suitable one. See [Vehicle Types](https://developers.gophr.com/docs/vehicle-types) for the limits of each vehicle.

### Parcel Handling Flags

| Flag | Effect |
| ---- | ------ |
| `is_fragile`, `is_glass`, `is_baked`, `is_flower` | Handle with care. These may move the job to a car. Glass is never sent by pushbike |
| `is_not_rotatable` | Keep the parcel upright |
| `is_food`, `is_fragile`, `is_alcohol`, `is_beef`, `is_pork` | Only offered to couriers who have agreed to carry these goods |
| `is_liquid` | The parcel contains liquid |
| `id_check` | The courier checks the recipient's identity |

### Optional Features

**Multiple identical parcels.** Set `quantity` (up to 1000, and 1000 per request) to book several identical parcels from one entry. `weight` is the total for the entry and is split evenly between the parcels. Dropoffs reference the entry by its `parcel_external_id` as usual.

**Delivery PIN.** Set `pin_required` to `1` on a dropoff. The recipient receives a code by SMS and email, and the courier must enter it before handing over the goods. This may add a surcharge.

**Age check.** Set `min_required_age` (up to 25) on a dropoff for age-restricted goods. The courier checks the recipient's ID, and returns the goods if the check fails.

**Proof of pickup and delivery.** Set `pickup_proof_required` or `dropoff_proof_required` to ask the courier for a photo, a signature or both.

**Safe place.** `fallback_handover_method`, `safe_place_location` and `leave_safe_instructions` tell the courier what to do if nobody can receive the goods. They can only be set while the job is a draft.

**Finding the address.** Add `pickup_w3w` or `dropoff_w3w` (a [what3words](https://what3words.com/) address), coordinates (`*_location_lat`, `*_location_lng`), or directions in `*_tips_how_to_find`.

**Dangerous goods.** Set `dangerous_goods` on a parcel to declare goods regulated under ADR, such as lithium batteries. This feature must be enabled for your account, and requests are rejected otherwise. See [Dangerous Goods](https://developers.gophr.com/docs/dangerous-goods) for the values, limits and packaging rules.

**Metadata.** `meta_data` is a list of `key` and `value` strings stored with the job. The client adds `{"key": "booking_method", "value": "shimango"}` to every POST request unless you send your own `booking_method` entry.

## Reading Responses

Every method returns a response object with the usual PSR-7 methods (`getStatusCode()`, `getHeaders()`, `getBody()`) and two ways to read the body:

```php
$response = $gophr->getJob($jobId);

// Typed objects, with autocompletion in your IDE
$job = $response->getContentsObject()->data;
echo $job->price_gross->amount;

// Plain arrays, exactly as the API returned them
$body = $response->getContentsArray();
echo $body['data']['price_gross']['amount'];
```

- The API wraps every result in `data`. `listJobs()` also returns `links` (`first`, `previous`, `next`) and `meta.page` (`current_page`, `count`, `total`).
- On an error response, `getContentsObject()->data` is `null` and the details are in `getContentsArray()['errors']`.
- New fields that the API adds are ignored by the typed objects, so your code keeps working. Read them with `getContentsArray()`.
- Both methods read the body stream. Use them instead of `getBody()->getContents()`, which can only be read once.

### Delivery Statuses

| Status | Meaning |
| ------ | ------- |
| `NEW` | Draft, not yet confirmed |
| `QUOTE_REJECTED` | The quote was rejected |
| `CONFIRMED_BY_CUSTOMER` | Booked and waiting for a courier |
| `COURIER_PROPOSED` | Offered to a courier |
| `REJECTED_BY_COURIER` | The courier declined, so the job is offered again |
| `ACCEPTED_BY_COURIER` | A courier has accepted the job |
| `AT_PICKUP` | The courier is at the pickup |
| `EN_ROUTE` | On the way to the dropoff |
| `AT_DELIVERY` | The courier is at the dropoff |
| `TAKING_SIGNATURE` | The courier is taking the recipient's signature |
| `DELIVERED` | Delivered |
| `EN_BACK_ROUTE` | Being returned to the pickup |
| `RETURNED` | Returned to the pickup |
| `DAMAGED` | The goods were damaged |
| `STOLEN` | The goods were stolen |
| `CANCELED` | Cancelled |

## Handling Errors

The client does not throw on HTTP errors. A 4xx or 5xx response is returned like a successful one, so always check the status code.

```php
$response = $gophr->createJob($jobData);
$status = $response->getStatusCode();

if ($status >= 200 && $status < 300) {
    $job = $response->getContentsObject()->data;
} elseif ($status === 0) {
    // The request never got a response, e.g. a timeout or a DNS failure
} else {
    foreach ($response->getContentsArray()['errors'] ?? [] as $error) {
        echo "{$error['code']}: {$error['message']}\n";
    }
}
```

Errors look like this. `object` points to the field that failed, when there is one:

```json
{
  "errors": [
    {
      "object": "dropoffs.0.dropoff_postcode",
      "code": "VALIDATION_FAILED",
      "message": "dropoffs.0.dropoff_postcode field must be present in the request"
    }
  ]
}
```

| Status | Meaning |
| ------ | ------- |
| `401` | The API key is missing or invalid |
| `403` | The API key cannot access this job |
| `404` | The job, delivery or parcel does not exist |
| `422` | The request failed validation (`VALIDATION_FAILED`), or the action is not allowed in the job's current state, e.g. changing a parcel on a confirmed job (`INVALID_JOB_OPERATION`) |
| `0` | No response was received |

The client only throws a `Shimango\Gophr\Exceptions\GophrException` for problems in your own setup, such as an empty API key:

```php
use Shimango\Gophr\Exceptions\GophrException;

try {
    $response = $gophr->getJob($jobId);
} catch (GophrException $exception) {
    echo "Client misconfigured: {$exception->getMessage()}\n";
}
```

## Receiving Webhooks

Gophr can call your server when a delivery's status or ETA changes. Set the webhook URLs in the API key settings of the Gophr web app. Each call includes your API key in the `Api-Key` header, so check it before trusting the payload.

```php
$apiKey = 'YOUR_API_KEY';

if (hash_equals($apiKey, $_SERVER['HTTP_API_KEY'] ?? '') === false) {
    http_response_code(401);
    exit;
}

$event = json_decode(file_get_contents('php://input'), true);

// e.g. update your order using $event['external_id'], $event['delivery_id'] and $event['status']

http_response_code(200);
```

Webhook payloads contain status, ETA, price, courier and proof of delivery details, but not the pickup or dropoff addresses. Call `getDelivery()` if you need them. See [Webhooks](https://developers.gophr.com/docs/webhooks) for the full payload.

## Testing Your Integration

In the sandbox no real courier picks up the job. `progressDeliveryStatus()` moves a delivery to its next status each time you call it, so you can test how your system handles every stage, including your webhooks.

```php
$gophr = new Client(new Configuration('YOUR_SANDBOX_API_KEY', true));

$job = $gophr->createJob(array_merge($jobData, ['is_confirmed' => 1]))->getContentsObject()->data;
$delivery = $job->deliveries[0];

do {
    $response = $gophr->progressDeliveryStatus($job->job_id, $delivery->delivery_id);
    $status = $response->getContentsObject()->data->status ?? null;
    echo "Status: {$status}\n";
} while ($response->getStatusCode() === 200 && $status !== 'DELIVERED');
```

`progressDeliveryStatus()` is not available in production.

## Method Reference

| Method | API call |
| ------ | -------- |
| `getQuote(array $jobData)` | `POST /quotes` |
| `createJob(array $jobData)` | `POST /jobs` |
| `getJob(string $jobId)` | `GET /jobs/{job_id}` |
| `updateJob(string $jobId, array $jobData)` | `PATCH /jobs/{job_id}` |
| `cancelJob(string $jobId, array $jobData)` | `POST /jobs/{job_id}/cancel` |
| `listJobs(int $page = 1, int $count = 15, bool $include_finished = false)` | `GET /jobs` |
| `createDelivery(string $jobId, array $deliveryData)` | `POST /jobs/{job_id}/deliveries` |
| `getDelivery(string $jobId, string $deliveryId)` | `GET /jobs/{job_id}/deliveries/{delivery_id}` |
| `updateDelivery(string $jobId, string $deliveryId, array $deliveryData)` | `PATCH /jobs/{job_id}/deliveries/{delivery_id}` |
| `listDeliveries(string $jobId)` | `GET /jobs/{job_id}/deliveries` |
| `cancelDelivery(string $jobId, string $deliveryId, array $deliveryData)` | `POST /jobs/{job_id}/deliveries/{delivery_id}/cancel` |
| `generateLabel(string $jobId, string $deliveryId, array $labelData = [])` | `POST /jobs/{job_id}/deliveries/{delivery_id}/label` |
| `progressDeliveryStatus(string $jobId, string $deliveryId)` | `POST /jobs/{job_id}/deliveries/{delivery_id}/progress` (sandbox only) |
| `createParcel(string $jobId, string $deliveryId, array $parcelData)` | `POST /jobs/{job_id}/deliveries/{delivery_id}/parcels` |
| `getParcel(string $jobId, string $deliveryId, string $parcelId)` | `GET /jobs/{job_id}/deliveries/{delivery_id}/parcels/{parcel_id}` |
| `updateParcel(string $jobId, string $deliveryId, string $parcelId, array $parcelData)` | `PATCH /jobs/{job_id}/deliveries/{delivery_id}/parcels/{parcel_id}` |
| `listParcels(string $jobId, string $deliveryId)` | `GET /jobs/{job_id}/deliveries/{delivery_id}/parcels` |
| `deleteParcel(string $jobId, string $deliveryId, string $parcelId)` | `DELETE /jobs/{job_id}/deliveries/{delivery_id}/parcels/{parcel_id}` |
| `getBaseApiEndpoint()` | Returns the base URL in use |

## Development

```bash
composer install
composer test           # run the test suite
composer test-coverage  # write an HTML coverage report to ./coverage
```

Contributions are welcome. Please open an issue or a pull request on [GitHub](https://github.com/shimango/gophr), and add tests for any change in behaviour.

## Support

- [Gophr API documentation](https://developers.gophr.com/docs)
- [Gophr API reference](https://developers.gophr.com/reference)
- [Issue tracker](https://github.com/shimango/gophr/issues)
- [Packagist](https://packagist.org/packages/shimango/gophr)

See [CHANGELOG.md](CHANGELOG.md) for the version history. This library is released under the [MIT License](LICENSE).
