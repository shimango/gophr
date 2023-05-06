<?php

namespace Shimango\Gophr\Resources;

use Shimango\Gophr\Exceptions\InvalidReturnTypeException;
use Shimango\Gophr\Http\Responses\Parcels\CreateParcelResponse;
use Shimango\Gophr\Http\Responses\Parcels\DeleteParcelResponse;
use Shimango\Gophr\Http\Responses\Parcels\GetParcelResponse;
use Shimango\Gophr\Http\Responses\Parcels\ListParcelsResponse;
use Shimango\Gophr\Http\Responses\Parcels\UpdateParcelResponse;

/**
 * A parcel resource represents the data associated with a physical parcel. A parcel belongs to a delivery. A delivery
 * can have many parcels.
 * @package Shimango\Gophr\Resources
 */
class Parcel extends AbstractResource
{
    /**
     * Creates a parcel
     * @throws InvalidReturnTypeException
     */
    public function createParcel(string $jobId, string $deliveryId, array $parcelData): CreateParcelResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . sprintf('/jobs/%s/deliveries/%s/parcels', $jobId, $deliveryId) . $this->generateUrlParameters();
        /** @var CreateParcelResponse $response */
        $response = $this->create($apiEndPoint, $parcelData, CreateParcelResponse::class);
        return $response;
    }

    /**
     * Gets a parcel
     * @throws InvalidReturnTypeException
     */
    public function getParcel(string $jobId, string $deliveryId, string $parcelId): GetParcelResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . sprintf('/jobs/%s/deliveries/%s/parcels/%s', $jobId, $deliveryId, $parcelId) . $this->generateUrlParameters();
        /** @var GetParcelResponse $response */
        $response = $this->read($apiEndPoint, GetParcelResponse::class);
        return $response;
    }

    /**
     * Updates a parcel
     * @throws InvalidReturnTypeException
     */
    public function updateParcel(string $jobId, string $deliveryId, string $parcelId, array $deliveryData): UpdateParcelResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . sprintf('/jobs/%s/deliveries/%s/parcels/%s', $jobId, $deliveryId, $parcelId) . $this->generateUrlParameters();
        /** @var UpdateParcelResponse $response */
        $response = $this->update($apiEndPoint, $deliveryData, UpdateParcelResponse::class);
        return $response;
    }

    /**
     * deletes a parcel
     * @throws InvalidReturnTypeException
     */
    public function deleteParcel(string $jobId, string $deliveryId, string $parcelId): DeleteParcelResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . sprintf('/jobs/%s/deliveries/%s/parcels/%s', $jobId, $deliveryId, $parcelId) . $this->generateUrlParameters();
        /** @var DeleteParcelResponse $response */
        $response = $this->delete($apiEndPoint, DeleteParcelResponse::class);
        return $response;
    }

    /**
     * Lists parcels
     * @throws InvalidReturnTypeException
     */
    public function listParcels(string $jobId, string $deliveryId): ListParcelsResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . sprintf('/jobs/%s/deliveries/%s/parcels', $jobId, $deliveryId) . $this->generateUrlParameters();
        /** @var ListParcelsResponse $response */
        $response = $this->read($apiEndPoint, ListParcelsResponse::class);
        return $response;
    }
}
