<?php

namespace Shimango\Gophr\Resources;

use Shimango\Gophr\Exceptions\InvalidReturnTypeException;
use Shimango\Gophr\Http\Responses\Deliveries\CancelDeliveryResponse;
use Shimango\Gophr\Http\Responses\Deliveries\CreateDeliveryResponse;
use Shimango\Gophr\Http\Responses\Deliveries\GetDeliveryResponse;
use Shimango\Gophr\Http\Responses\Deliveries\ListDeliveriesResponse;
use Shimango\Gophr\Http\Responses\Deliveries\UpdateDeliveryResponse;

/**
 * A delivery represents a specific pickup / dropoff combination. A delivery can have many parcels and belongs to a job.
 * @package Shimango\Gophr\Resources
 */
class Delivery extends AbstractResource
{
    /**
     * Creates delivery
     * @param string $jobId
     * @param array $deliveryData
     * @return CreateDeliveryResponse
     * @throws InvalidReturnTypeException
     */
    public function createDelivery(string $jobId, array $deliveryData): CreateDeliveryResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}/deliveries";
        /** @var CreateDeliveryResponse $response */
        $response = $this->create($apiEndPoint, $deliveryData, CreateDeliveryResponse::class);
        return $response;
    }

    /**
     * Gets a delivery
     * @param string $jobId
     * @param string $deliveryId
     * @return GetDeliveryResponse
     * @throws InvalidReturnTypeException
     */
    public function getDelivery(string $jobId, string $deliveryId): GetDeliveryResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}/deliveries/{$deliveryId}";
        /** @var GetDeliveryResponse $response */
        $response = $this->read($apiEndPoint, GetDeliveryResponse::class);
        return $response;
    }

    /**
     * Updates a delivery
     * @param string $jobId
     * @param string $deliveryId
     * @param array $deliveryData
     * @return UpdateDeliveryResponse
     * @throws InvalidReturnTypeException
     */
    public function updateDelivery(string $jobId, string $deliveryId, array $deliveryData): UpdateDeliveryResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}/deliveries/{$deliveryId}";
        /** @var UpdateDeliveryResponse $response */
        $response = $this->update($apiEndPoint, $deliveryData, UpdateDeliveryResponse::class);
        return $response;
    }

    /**
     * Lists deliveries
     * @param string $jobId
     * @return ListDeliveriesResponse
     * @throws InvalidReturnTypeException
     */
    public function listDeliveries(string $jobId): ListDeliveriesResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}/deliveries";
        /** @var ListDeliveriesResponse $response */
        $response = $this->read($apiEndPoint, ListDeliveriesResponse::class);
        return $response;
    }

    /**
     * Cancels a delivery
     * @param string $jobId
     * @param string $deliveryId
     * @param array $deliveryData
     * @return CancelDeliveryResponse
     * @throws InvalidReturnTypeException
     */
    public function cancelDelivery(string $jobId, string $deliveryId, array $deliveryData): CancelDeliveryResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}/deliveries/{$deliveryId}/cancel";
        /** @var CancelDeliveryResponse $response */
        $response = $this->create($apiEndPoint, $deliveryData, CancelDeliveryResponse::class);
        return $response;
    }
}
