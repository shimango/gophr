# Gophr PHP Client Library

A comprehensive PHP client for the Gophr Courier Service API, enabling seamless integration of courier services into your PHP applications.

[![Build Status](https://github.com/shimango/gophr/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/shimango/gophr/actions/workflows/tests.yml?query=branch%3Amaster)
[![Latest Stable Version](http://poser.pugx.org/shimango/gophr/v)](https://packagist.org/packages/shimango/gophr)
[![Total Downloads](http://poser.pugx.org/shimango/gophr/downloads)](https://packagist.org/packages/shimango/gophr)
[![License](http://poser.pugx.org/shimango/gophr/license)](https://packagist.org/packages/shimango/gophr)
[![PHP Version Require](http://poser.pugx.org/shimango/gophr/require/php)](https://packagist.org/packages/shimango/gophr)

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Core Concepts](#core-concepts)
- [Usage Examples](#usage-examples)
  - [Working with Jobs](#working-with-jobs)
  - [Working with Deliveries](#working-with-deliveries)
  - [Working with Parcels](#working-with-parcels)
- [Response Handling](#response-handling)
- [Error Handling](#error-handling)
- [Testing](#testing)
- [API Reference](#api-reference)

## Requirements

- PHP 8.0 or higher
- Composer
- A Gophr API key (obtain from [Gophr Authorization](https://developers.gophr.com/docs/authorisation))

## Installation

### Using Composer (Recommended)

```bash
composer require shimango/gophr
```

### Manual Installation

1. Download this package from GitHub
2. Run `composer install` in the package directory
3. Require the autoloader in your project:

```php
require_once 'vendor/autoload.php';
```

## Quick Start

Here's a simple example to get you started:

```php
<?php
require_once 'vendor/autoload.php';

use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;

// Initialize the client
$config = new Configuration('YOUR_API_KEY');
$gophr = new Client($config);

// List all jobs
$response = $gophr->listJobs();
$jobs = $response->getContentsArray();

foreach ($jobs['data']['jobs'] as $job) {
    echo "Job ID: {$job['job_id']} - Confirmed: {$job['is_confirmed']}\n";

    foreach ($job['deliveries'] as $delivery) {
        echo "  Delivery {$delivery['delivery_id']}: {$delivery['status']}\n";
    }
}
```

## Configuration

The `Configuration` class manages all settings for API communication.

### Constructor Parameters

```php
$config = new Configuration(
    $apiKey,           // Required: Your Gophr API key
    $isSandbox,        // Optional: Use sandbox environment (default: false)
    $apiVersion,       // Optional: API version (default: "v2")
    $proxyPort,        // Optional: Port of a proxy on localhost (default: null)
    $proxyVerifySSL    // Optional: Verify SSL when using the proxy (default: false)
);
```

For local development, set the `REST_BASE_URL_DEVELOPMENT` environment variable to send requests to another base URL. Set `GOPHR_XDEBUG_SESSION` to add an `XDEBUG_SESSION` query parameter to every request.

### Configuration Examples

**Production Environment:**
```php
$config = new Configuration('live_api_key_here');
```

**Sandbox/Testing Environment:**
```php
$config = new Configuration('test_api_key_here', true);
```

**Using a Proxy:**
```php
$config = new Configuration(
    'your_api_key',
    false,              // Production
    'v2',
    8080,              // Requests go through http://localhost:8080
    true               // Verify SSL
);
```

## Core Concepts

### Jobs
A **Job** represents a complete courier assignment that may include multiple deliveries. All deliveries within a job are completed by the same courier in a single trip.

### Deliveries
A **Delivery** consists of:
- One pickup location
- One dropoff location
- Associated parcels to be transported

### Parcels
**Parcels** are the individual packages within a delivery. A delivery can contain multiple parcels.

### Workflow
1. Create a Job with a pickup, deliveries, and parcels. This can be done in two ways:
    - **All at once**: Create and confirm the Job in one go.
    - **As a draft**: Create the Job as a draft and edit it (e.g., add/remove/edit deliveries and parcels) until it is ready to be confirmed.
2. Confirm the Job to dispatch the courier.

**Note:** While a job is a draft, everything can be changed. Once it is confirmed, you can no longer add deliveries, and only a delivery's contact and instruction fields can be updated (see [Updating a Delivery](#updating-a-delivery)).

## Usage Examples

### Working with Jobs

#### Creating a Job

```php
use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;

$config = new Configuration('YOUR_API_KEY', true); // Sandbox mode
$gophr = new Client($config);

$jobData = [
    'external_id' => '2112',
    'is_confirmed' => 1,
    'pickups' => [
        [
            'earliest_pickup_time' => '2026-08-12T06:17:00+00:00',
            'pickup_deadline' => '2026-08-12T07:13:00+00:00',
            'pickup_address1' => '221B Baker Street',
            'pickup_city' => 'London',
            'pickup_postcode' => 'NW1 6TS',
            'pickup_country_code' => 'GB',
            'pickup_company_name' => 'Elementary Investigations',
            'pickup_person_name' => 'Sherlock Holmes',
            'pickup_email' => 'itiselementary@test.com',
            'pickup_mobile_number' => '07700000000',
            'parcels' => [
                [
                    'parcel_external_id' => '001',
                    'parcel_reference_number' => '0fc35278-60a1-4fb3-9791-4f45c492e120',
                    'parcel_description' => 'Sunglasses',
                    'width' => 10,
                    'length' => 15,
                    'height' => 5,
                    'weight' => 0.5,
                ],
                [
                    'parcel_external_id' => '002',
                    'parcel_reference_number' => '0fc35278-60a1-4fb3-9791-4f45c492e120',
                    'parcel_description' => 'Folding chair',
                    'width' => 50,
                    'length' => 80,
                    'height' => 5,
                    'weight' => 1.5,
                ],
            ],
        ],
    ],
    'dropoffs' => [
        [
            'dropoff_company_name' => 'The Prime Minister\'s Office',
            'dropoff_address1' => '10 Downing Street',
            'dropoff_city' => 'London',
            'dropoff_postcode' => 'SW1A 0AA',
            'dropoff_country_code' => 'GB',
            'dropoff_person_name' => 'The prime minister',
            'dropoff_email' => 'pm@test.com',
            'dropoff_mobile_number' => '07588000000',
            'dropoff_deadline' => '2026-08-12T08:00:00+00:00',
            'parcels' => [
                [
                    'parcel_external_id' => '001',
                ],
                [
                    'parcel_external_id' => '002',
                ],
            ],
        ],
    ],
    'meta_data' => [
        ['key' => 'order_number', 'value' => 'ORD-2112'],
    ],
];

$response = $gophr->createJob($jobData);
$job = $response->getContentsObject()->data;

echo "Created Job ID: {$job->job_id}\n";
echo "Price: {$job->price_gross->amount} {$job->price_gross->currency}\n";
```

`meta_data` is optional and must be a list of `key`/`value` string pairs. The client adds a `booking_method` entry with the value `shimango` to every POST request unless you send your own `booking_method` entry.

#### Getting a Quote

`getQuote()` takes the same payload as `createJob()` and returns the price and ETAs without creating a job.

```php
$response = $gophr->getQuote($jobData);
$quote = $response->getContentsObject()->data;

echo "Price: {$quote->price_gross->amount} {$quote->price_gross->currency}\n";
echo "Pickup ETA: {$quote->pickup_eta} - Delivery ETA: {$quote->delivery_eta}\n";
```

#### Retrieving a Job

```php
$jobId = 'job_abc123';
$response = $gophr->getJob($jobId);
$job = $response->getContentsArray();

print_r($job);
```

#### Updating a Job (Confirming)

```php
$jobId = 'job_abc123';
$updateData = [
    'is_confirmed' => 1
];

$response = $gophr->updateJob($jobId, $updateData);
$updatedJob = $response->getContentsObject()->data;

echo "Job confirmed: {$updatedJob->is_confirmed}\n";
```

#### Cancelling a Job

`cancelled_reason` is one of `ORDER_CANCELLED`, `WRONG_INFO`, `COURIER_LATE`, `DUPLICATED`, `FRAUDULENT_ORDER`, `NOT_ACCEPTED`, `NRT`, `RESCHEDULED`, `TECHNICAL_ISSUES`, `TEST_ORDER` or `NO_NEED`.

```php
$jobId = 'job_abc123';
$cancelData = [
    'cancelled_reason' => 'ORDER_CANCELLED',
    'cancelled_comment' => 'Customer requested cancellation',
];

$response = $gophr->cancelJob($jobId, $cancelData);
$cancellationCost = $response->getContentsObject()->data->cancelation_cost;

echo "Cancellation cost: {$cancellationCost->amount} {$cancellationCost->currency}\n";
```

#### Listing Jobs with Pagination

```php
// Get page 2, with 20 jobs per page, including finished jobs
$page = 2;
$count = 20;
$includeFinished = true;

$response = $gophr->listJobs($page, $count, $includeFinished);
$jobs = $response->getContentsArray();

echo "Total jobs: {$jobs['meta']['page']['total']}\n";
echo "Current page: {$jobs['meta']['page']['current_page']}\n";
echo "Next page: {$jobs['links']['next']}\n";

foreach ($jobs['data']['jobs'] as $job) {
    echo "- Job {$job['job_id']}: " . count($job['deliveries']) . " deliveries\n";
}
```

`$count` can be between 1 and 200.

### Working with Deliveries

#### Adding a Delivery to a Job

Deliveries can only be added while the job is a draft.

```php
$jobId = 'job_abc123';

$deliveryData = [
    'pickup' => [
        'earliest_pickup_time' => '2026-08-12T06:17:00+00:00',
        'pickup_deadline' => '2026-08-12T07:13:00+00:00',
        'pickup_address1' => '221B Baker Street',
        'pickup_city' => 'London',
        'pickup_postcode' => 'NW1 6TS',
        'pickup_country_code' => 'GB',
        'pickup_company_name' => 'Gophr',
        'pickup_person_name' => 'John Smith',
        'pickup_email' => 'john@test.com',
        'pickup_mobile_number' => '07700000000',
    ],
    'dropoff' => [
        'dropoff_company_name' => 'Buckingham Palace',
        'dropoff_address1' => 'Buckingham Palace',
        'dropoff_city' => 'London',
        'dropoff_postcode' => 'SW1A 1AA',
        'dropoff_country_code' => 'GB',
        'dropoff_person_name' => 'The Royal Household',
        'dropoff_email' => 'yourmajesty@test.com',
        'dropoff_mobile_number' => '07700000000',
        'dropoff_deadline' => '2026-08-12T08:00:00+00:00',
    ],
    'parcels' => [
        [
            'parcel_external_id' => '003',
            'parcel_reference_number' => '0fc35278-60a1-4fb3-9791-4f45c492e120',
            'parcel_description' => 'Sunglass',
            'width' => 10,
            'length' => 15,
            'height' => 5,
            'weight' => 0.5,
        ],
        [
            'parcel_external_id' => '004',
            'parcel_reference_number' => '0fc35278-60a1-4fb3-9791-4f45c492e120',
            'parcel_description' => 'Folding chair',
            'width' => 50,
            'length' => 80,
            'height' => 5,
            'weight' => 1.5,
        ],
    ],
];

$response = $gophr->createDelivery($jobId, $deliveryData);
$delivery = $response->getContentsObject()->data;

echo "Created Delivery ID: {$delivery->delivery_id}\n";
```

#### Retrieving a Delivery

The job endpoints return a summary of each delivery. Use the delivery endpoints to get the full pickup and dropoff details.

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$response = $gophr->getDelivery($jobId, $deliveryId);
$delivery = $response->getContentsArray();

print_r($delivery);
```

#### Updating a Delivery

While the job is a draft, any field can be updated. Once it is confirmed, only these pickup and dropoff fields can be changed: `instructions`, `tips_how_to_find`, `person_name`, `company_name`, `mobile_number`, `phone_number` and `email` (with the `pickup_` or `dropoff_` prefix). Pickup fields can no longer be changed once the courier is at the pickup, and nothing can be changed once the courier is at the dropoff.

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$updateData = [
    'dropoff' => [
        'dropoff_phone_number' => '+44 20 7999 8888'  // Update contact phone
    ]
];

$response = $gophr->updateDelivery($jobId, $deliveryId, $updateData);
echo "Delivery updated successfully\n";
```

#### Listing Deliveries for a Job

```php
$jobId = 'job_abc123';

$response = $gophr->listDeliveries($jobId);
$deliveries = $response->getContentsArray();

foreach ($deliveries['data']['deliveries'] as $delivery) {
    echo "Delivery {$delivery['delivery_id']}: {$delivery['status']}\n";
}
```

#### Cancelling a Delivery

The cancellation reasons are the same as for [cancelling a job](#cancelling-a-job).

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$cancelData = [
    'cancelled_reason' => 'WRONG_INFO',
    'cancelled_comment' => 'Address incorrect',
];

$response = $gophr->cancelDelivery($jobId, $deliveryId, $cancelData);
echo "Delivery cancelled\n";
```

#### Progressing a Delivery Status (Sandbox Only)

Moves a delivery to its next status so you can test your integration. This is not available in production.

```php
$response = $gophr->progressDeliveryStatus($jobId, $deliveryId);
echo "New status: {$response->getContentsObject()->data->status}\n";
```

### Working with Parcels

#### Adding a Parcel to a Delivery

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$parcelData = [
    'parcel_external_id' => '005',
    'parcel_description' => 'Electronics',
    'weight' => 2.5,        // kg
    'length' => 40,         // cm
    'width' => 30,          // cm
    'height' => 20,         // cm
    'is_fragile' => 1
];

$response = $gophr->createParcel($jobId, $deliveryId, $parcelData);
$parcel = $response->getContentsObject()->data;

echo "Created Parcel ID: {$parcel->parcel_id}\n";
```

#### Retrieving a Parcel

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';
$parcelId = 'parcel_def456';

$response = $gophr->getParcel($jobId, $deliveryId, $parcelId);
$parcel = $response->getContentsArray();

print_r($parcel);
```

#### Updating a Parcel

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';
$parcelId = 'parcel_def456';

$updateData = [
    'weight' => 3.0,  // Update weight
    'is_not_rotatable' => 1 // Mark as non-rotatable
];

$response = $gophr->updateParcel($jobId, $deliveryId, $parcelId, $updateData);
echo "Parcel updated\n";
```

#### Listing Parcels in a Delivery

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$response = $gophr->listParcels($jobId, $deliveryId);
$parcels = $response->getContentsArray();

foreach ($parcels['data']['parcels'] as $parcel) {
    echo "Parcel {$parcel['parcel_id']}: {$parcel['parcel_description']} - {$parcel['weight']}kg\n";
}
```

#### Deleting a Parcel

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';
$parcelId = 'parcel_def456';

$response = $gophr->deleteParcel($jobId, $deliveryId, $parcelId);
echo "Parcel deleted\n";
```

## Response Handling

Each method returns its own response class (for example `GetJobResponse`), which follows the PSR-7 response methods. The API wraps the result in a `data` key, and list jobs also returns `links` and `meta`.

### Getting Response Data

```php
// As an array
$data = $response->getContentsArray();
$jobId = $data['data']['job_id'];

// As a typed data transfer object
$job = $response->getContentsObject()->data;
$jobId = $job->job_id;

// Get status code
$statusCode = $response->getStatusCode();
```

`getContentsObject()` returns `null` when the body does not match the expected shape (for example on an error response), so check the status code first. Both methods read the body stream, so use them instead of `getBody()->getContents()`.

### Response Example

```php
$response = $gophr->getJob('job_abc123');

if ($response->getStatusCode() === 200) {
    $job = $response->getContentsObject()->data;

    echo "Job ID: {$job->job_id}\n";
    echo "Vehicle type: {$job->vehicle_type}\n";
    echo "Price: {$job->price_net->amount} {$job->price_net->currency}\n";

    foreach ($job->deliveries as $delivery) {
        echo "  Delivery {$delivery->delivery_id}: {$delivery->status}\n";
        echo "  Tracking: {$delivery->public_tracker_url}\n";
    }
}
```

## Error Handling

HTTP errors do not throw. A 4xx or 5xx response is returned like any other response, so check the status code before reading the data.

If the request never gets a response (for example a timeout or a DNS failure), the client returns a response with an empty body and a status code of `0` or the cURL error code.

```php
use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;
use Shimango\Gophr\Exceptions\GophrException;

$config = new Configuration('YOUR_API_KEY', true);
$gophr = new Client($config);

try {
    $response = $gophr->getJob('invalid_job_id');
} catch (GophrException $e) {
    // Thrown for client setup problems, such as an empty API key
    echo "Client error: {$e->getMessage()}\n";
    return;
}

$statusCode = $response->getStatusCode();

if ($statusCode >= 200 && $statusCode < 300) {
    $job = $response->getContentsObject()->data;
    echo "Job ID: {$job->job_id}\n";
} elseif ($statusCode >= 400) {
    // Error responses contain JSON describing what went wrong
    echo "API error ({$statusCode}):\n";
    print_r($response->getContentsArray());
} else {
    echo "No response from the API ({$statusCode})\n";
}
```

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

## API Reference

For detailed API documentation, request/response schemas, and additional features, visit:

- [Gophr API Documentation](https://developers.gophr.com/docs)
- [Gophr API Reference](https://developers.gophr.com/reference)

## Best Practices

1. **Always use sandbox mode during development:**
   ```php
   $config = new Configuration('test_api_key', true);
   ```

2. **Check the status code of every response:**
   HTTP errors are returned as responses, not thrown

3. **Validate data before sending:**
   Ensure all required fields are present before making API calls

4. **Store job and delivery IDs:**
   Keep track of `job_id` and `delivery_id` in your database for future reference

5. **Finish editing before you confirm:**
   After confirmation, only a delivery's contact and instruction fields can be updated

6. **Use pagination for lists:**
   When retrieving lists of jobs, use pagination to manage large datasets

## Support and Resources

- **API Documentation:** https://developers.gophr.com/docs
- **Issue Tracker:** https://github.com/shimango/gophr/issues
- **Packagist:** https://packagist.org/packages/shimango/gophr

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and changes.
