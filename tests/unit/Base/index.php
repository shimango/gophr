<?php

use Shimango\Gophr\Client;
use Shimango\Gophr\Common\Configuration;
use Shimango\Gophr\Http\AbstractGophrResponse;
use Shimango\Gophr\Tests\Unit\Base\Payloads;

require_once(__DIR__ . '/../../../vendor/autoload.php');

function validateResponse(AbstractGophrResponse $response, string $action = "#"): void
{
    $formatSuccess = (php_sapi_name() == "cli") ? "\033[32m[\u{221a}] \033[0m%s\n" : "[\u{221a}] %s <br />";
    $formatError =   (php_sapi_name() == "cli") ? "\033[31m[\u{2717}] \033[0m%s\n"  : "[\u{2717}] %s <br />";

    if (in_array($response->getStatusCode(),  [200, 201])) {
        echo sprintf($formatSuccess, $action); // "\033[32m[\u{221a}] \033[0m{$action}\n";
    } else {
        echo sprintf($formatError, $action);
        var_dump($response->getContentsArray());
        exit(1);
    }
}

$config = new Configuration('dev-00bc576e-4549-40af-91d5-71360758d701', true);
$config->setApiVersion('v2');

$gophr = new Client($config); // Set your own API access key here.

/** **************************************************************************************************************** **/

$response = $gophr->getQuote(Payloads::getCreateJobPayload());
validateResponse($response, "getQuote");

$response1 = $gophr->createJob(Payloads::getCreateJobPayload());
validateResponse($response1, "createJob");
$arr1 = $response1->getContentsArray();
$obj1 = $response1->getContentsObject();

$job_id = $obj1->data->job_id;

$response2 = $gophr->getJob($job_id);
validateResponse($response2, "getJob");
$arr2 = $response2->getContentsArray();
$obj2 = $response2->getContentsObject();

$response3 = $gophr->listJobs();
validateResponse($response3, "listJobs");
$arr3 = $response3->getContentsArray();
$obj3 = $response3->getContentsObject();

$response4 = $gophr->createDelivery($job_id, Payloads::getCreateDeliveryPayload());
validateResponse($response4, "createDelivery");
$arr4 = $response4->getContentsArray();
$obj4 = $response4->getContentsObject();

$delivery_id = $obj4->data->delivery_id;

$response5 = $gophr->getDelivery($job_id, $delivery_id);
validateResponse($response5, "getDelivery");
$arr5 = $response5->getContentsArray();
$obj5 = $response5->getContentsObject();

$response6 = $gophr->updateDelivery($job_id, $delivery_id, Payloads::getUpdateDeliveryPayload());
validateResponse($response6, "updateDelivery");
$arr6 = $response6->getContentsArray();
$obj6 = $response6->getContentsObject();

$response7 = $gophr->listDeliveries($job_id);
validateResponse($response7, "listDeliveries");
$arr7 = $response7->getContentsArray();
$obj7 = $response7->getContentsObject();

$response8 = $gophr->createParcel($job_id, $delivery_id, Payloads::getCreateParcelPayload());
validateResponse($response8, "createParcel");
$arr8 = $response8->getContentsArray();
$obj8 = $response8->getContentsObject();

$parcel_id = $obj8->data->parcel_id;

$response9 = $gophr->updateParcel($job_id, $delivery_id, $parcel_id, Payloads::getUpdateParcelPayload());
validateResponse($response9, "updateParcel");
$arr9 = $response9->getContentsArray();
$obj9 = $response9->getContentsObject();

$response10 = $gophr->getParcel($job_id, $delivery_id, $parcel_id);
validateResponse($response10, "getParcel");
$arr10 = $response10->getContentsArray();
$obj10 = $response10->getContentsObject();

$response11 = $gophr->listParcels($job_id, $delivery_id);
validateResponse($response11, "listParcels");
$arr11 = $response11->getContentsArray();
$obj11 = $response11->getContentsObject();

$response12 = $gophr->deleteParcel($job_id, $delivery_id, $parcel_id);
validateResponse($response12, "deleteParcel");
$arr12 = $response12->getContentsArray();
$obj12 = $response12->getContentsObject();

$response13 = $gophr->cancelDelivery($job_id, $delivery_id, Payloads::getCancelDeliveryPayload());
validateResponse($response13, "cancelDelivery");
$arr13 = $response13->getContentsArray();
$obj13 = $response13->getContentsObject();

$response14 = $gophr->updateJob($job_id, Payloads::getUpdateJobPayload());
validateResponse($response14, "updateJob");
$arr14 = $response14->getContentsArray();
$obj14 = $response14->getContentsObject();

$response15= $gophr->cancelJob($job_id, Payloads::getCancelJobPayload());
validateResponse($response15, "cancelJob");
$arr15 = $response15->getContentsArray();
$obj15 = $response15->getContentsObject();