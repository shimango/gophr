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

- PHP 7.4 or higher
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

foreach ($jobs['data'] as $job) {
    echo "Job ID: {$job['id']} - Status: {$job['status']}\n";
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
    $proxyPort,        // Optional: Proxy port if needed (default: null)
    $proxyVerifySSL    // Optional: Verify SSL for proxy (default: false)
);
```

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
    8080,              // Proxy port
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
1. Create a Job
2. Add one or more Deliveries to the Job
3. Add Parcels to each Delivery
4. Confirm the Job to dispatch the courier

**Note:** Once a job is confirmed, deliveries and parcels cannot be modified.

## Usage Examples

### Working with Jobs

#### Creating a Job

```php
use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;

$config = new Configuration('YOUR_API_KEY', true); // Sandbox mode
$gophr = new Client($config);

$jobData = [
    'pickup' => [
        'address' => [
            'line_1' => '123 Pickup Street',
            'line_2' => 'Suite 100',
            'city' => 'London',
            'postcode' => 'SW1A 1AA',
            'country' => 'GB'
        ],
        'contact' => [
            'name' => 'John Sender',
            'phone' => '+44 20 7123 4567',
            'email' => 'sender@example.com'
        ]
    ],
    'dropoffs' => [
        [
            'address' => [
                'line_1' => '456 Delivery Road',
                'city' => 'London',
                'postcode' => 'EC1A 1BB',
                'country' => 'GB'
            ],
            'contact' => [
                'name' => 'Jane Receiver',
                'phone' => '+44 20 7987 6543',
                'email' => 'receiver@example.com'
            ],
            'parcels' => [
                [
                    'description' => 'Documents',
                    'weight' => 0.5,
                    'length' => 30,
                    'width' => 20,
                    'height' => 2
                ]
            ]
        ]
    ]
];

$response = $gophr->createJob($jobData);
$job = $response->getContentsObject();

echo "Created Job ID: {$job->id}\n";
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
    'status' => 'confirmed'
];

$response = $gophr->updateJob($jobId, $updateData);
$updatedJob = $response->getContentsObject();

echo "Job status: {$updatedJob->status}\n";
```

#### Cancelling a Job

```php
$jobId = 'job_abc123';
$cancelData = [
    'reason' => 'Customer requested cancellation'
];

$response = $gophr->cancelJob($jobId, $cancelData);
echo "Job cancelled successfully\n";
```

#### Listing Jobs with Pagination

```php
// Get page 2, with 20 jobs per page, including finished jobs
$page = 2;
$count = 20;
$includeFinished = true;

$response = $gophr->listJobs($page, $count, $includeFinished);
$jobs = $response->getContentsArray();

echo "Total jobs: {$jobs['total']}\n";
echo "Current page: {$jobs['current_page']}\n";

foreach ($jobs['data'] as $job) {
    echo "- Job {$job['id']}: {$job['status']}\n";
}
```

### Working with Deliveries

#### Adding a Delivery to a Job

```php
$jobId = 'job_abc123';

$deliveryData = [
    'pickup' => [
        'address' => [
            'line_1' => '789 Warehouse Lane',
            'city' => 'London',
            'postcode' => 'SE1 9SG',
            'country' => 'GB'
        ],
        'contact' => [
            'name' => 'Warehouse Manager',
            'phone' => '+44 20 7111 2222'
        ]
    ],
    'dropoff' => [
        'address' => [
            'line_1' => '321 Customer Avenue',
            'city' => 'London',
            'postcode' => 'N1 9AG',
            'country' => 'GB'
        ],
        'contact' => [
            'name' => 'Customer Name',
            'phone' => '+44 20 7333 4444'
        ]
    ]
];

$response = $gophr->createDelivery($jobId, $deliveryData);
$delivery = $response->getContentsObject();

echo "Created Delivery ID: {$delivery->id}\n";
```

#### Retrieving a Delivery

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$response = $gophr->getDelivery($jobId, $deliveryId);
$delivery = $response->getContentsArray();

print_r($delivery);
```

#### Updating a Delivery

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$updateData = [
    'dropoff' => [
        'contact' => [
            'phone' => '+44 20 7999 8888'  // Update contact phone
        ]
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

foreach ($deliveries as $delivery) {
    echo "Delivery {$delivery['id']}: {$delivery['status']}\n";
}
```

#### Cancelling a Delivery

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$cancelData = [
    'reason' => 'Address incorrect'
];

$response = $gophr->cancelDelivery($jobId, $deliveryId, $cancelData);
echo "Delivery cancelled\n";
```

### Working with Parcels

#### Adding a Parcel to a Delivery

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';

$parcelData = [
    'description' => 'Electronics',
    'weight' => 2.5,        // kg
    'length' => 40,         // cm
    'width' => 30,          // cm
    'height' => 20,         // cm
    'value' => 150.00,      // GBP
    'fragile' => true
];

$response = $gophr->createParcel($jobId, $deliveryId, $parcelData);
$parcel = $response->getContentsObject();

echo "Created Parcel ID: {$parcel->id}\n";
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
    'fragile' => false
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

foreach ($parcels as $parcel) {
    echo "Parcel {$parcel['id']}: {$parcel['description']} - {$parcel['weight']}kg\n";
}
```

#### Deleting a Parcel

```php
$jobId = 'job_abc123';
$deliveryId = 'delivery_xyz789';
$parcelId = 'parcel_def456';

$response = $gophr->deleteParcel($jobId, $deliveryId, $parcelId);
echo "Parcel deleted\n";

// Note: If this was the last parcel, the delivery will be automatically cancelled
```

## Response Handling

All API calls return a `GophrResponse` object that is PSR-7 compliant.

### Getting Response Data

```php
// As an array
$data = $response->getContentsArray();

// As an object
$data = $response->getContentsObject();

// Access specific fields
$jobId = $data['id'];          // Array access
$jobStatus = $data->status;    // Object access

// Get raw response
$rawBody = $response->getBody()->getContents();

// Get status code
$statusCode = $response->getStatusCode();
```

### Response Example

```php
$response = $gophr->getJob('job_abc123');

// Check if successful
if ($response->getStatusCode() === 200) {
    $job = $response->getContentsObject();
    
    echo "Job ID: {$job->id}\n";
    echo "Status: {$job->status}\n";
    echo "Created: {$job->created_at}\n";
    
    // Access nested data
    foreach ($job->deliveries as $delivery) {
        echo "  Delivery: {$delivery->id}\n";
    }
}
```

## Error Handling

Always wrap API calls in try-catch blocks to handle potential errors gracefully.

```php
use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;

$config = new Configuration('YOUR_API_KEY', true);
$gophr = new Client($config);

try {
    $response = $gophr->getJob('invalid_job_id');
    $job = $response->getContentsArray();
    
} catch (\GuzzleHttp\Exception\ClientException $e) {
    // 4xx errors (client errors)
    $statusCode = $e->getResponse()->getStatusCode();
    $errorBody = $e->getResponse()->getBody()->getContents();
    
    echo "Client Error ({$statusCode}): {$errorBody}\n";
    
} catch (\GuzzleHttp\Exception\ServerException $e) {
    // 5xx errors (server errors)
    echo "Server Error: " . $e->getMessage() . "\n";
    
} catch (\Exception $e) {
    // Other errors
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Common Error Scenarios

```php
// Validation error example
try {
    $jobData = [
        'pickup' => [
            // Missing required fields
        ]
    ];
    
    $response = $gophr->createJob($jobData);
    
} catch (\GuzzleHttp\Exception\ClientException $e) {
    if ($e->getResponse()->getStatusCode() === 422) {
        $errors = json_decode($e->getResponse()->getBody(), true);
        echo "Validation errors:\n";
        print_r($errors['errors']);
    }
}
```

## Using Resource Classes

Instead of using the main `Client` class, you can use individual resource classes for better organization:

```php
use Shimango\Gophr\Resources\Job;
use Shimango\Gophr\Resources\Delivery;
use Shimango\Gophr\Resources\Parcel;
use Shimango\Gophr\Common\Configuration;

$config = new Configuration('YOUR_API_KEY');

// Using Job resource
$jobResource = new Job($config);
$jobs = $jobResource->listJobs(1, 10);

// Using Delivery resource
$deliveryResource = new Delivery($config);
$delivery = $deliveryResource->getDelivery('job_id', 'delivery_id');

// Using Parcel resource
$parcelResource = new Parcel($config);
$parcels = $parcelResource->listParcels('job_id', 'delivery_id');
```

## Complete Workflow Example

Here's a complete example showing the typical workflow from job creation to confirmation:

```php
<?php
require_once 'vendor/autoload.php';

use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;

$config = new Configuration('YOUR_API_KEY', true);
$gophr = new Client($config);

try {
    // Step 1: Create a job
    $jobData = [
        'pickup' => [
            'address' => [
                'line_1' => '123 Main St',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
                'country' => 'GB'
            ],
            'contact' => [
                'name' => 'Shop Manager',
                'phone' => '+44 20 7123 4567'
            ]
        ],
        'dropoffs' => [
            [
                'address' => [
                    'line_1' => '456 Oak Ave',
                    'city' => 'London',
                    'postcode' => 'EC1A 1BB',
                    'country' => 'GB'
                ],
                'contact' => [
                    'name' => 'Customer',
                    'phone' => '+44 20 7987 6543'
                ]
            ]
        ]
    ];
    
    $jobResponse = $gophr->createJob($jobData);
    $job = $jobResponse->getContentsObject();
    echo "✓ Created Job: {$job->id}\n";
    
    // Step 2: Add a parcel to the delivery
    $deliveryId = $job->deliveries[0]->id;
    
    $parcelData = [
        'description' => 'Package',
        'weight' => 1.5,
        'length' => 30,
        'width' => 20,
        'height' => 10
    ];
    
    $parcelResponse = $gophr->createParcel($job->id, $deliveryId, $parcelData);
    $parcel = $parcelResponse->getContentsObject();
    echo "✓ Added Parcel: {$parcel->id}\n";
    
    // Step 3: Confirm the job to dispatch courier
    $confirmData = ['status' => 'confirmed'];
    $confirmResponse = $gophr->updateJob($job->id, $confirmData);
    $confirmedJob = $confirmResponse->getContentsObject();
    echo "✓ Job confirmed! Status: {$confirmedJob->status}\n";
    
    // Step 4: Track the job
    $trackResponse = $gophr->getJob($job->id);
    $trackedJob = $trackResponse->getContentsObject();
    echo "✓ Current status: {$trackedJob->status}\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
```

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test -- --coverage-html coverage
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

2. **Implement proper error handling:**
   Wrap all API calls in try-catch blocks

3. **Validate data before sending:**
   Ensure all required fields are present before making API calls

4. **Store job IDs:**
   Keep track of job IDs in your database for future reference

5. **Don't modify confirmed jobs:**
   Remember that jobs, deliveries, and parcels cannot be modified after confirmation

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
