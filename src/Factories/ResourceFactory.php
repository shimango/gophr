<?php

namespace Shimango\Gophr\Factories;

use Shimango\Gophr\Common\Configuration;
use Shimango\Gophr\Exceptions\GophrException;
use Shimango\Gophr\Resources\Delivery;
use Shimango\Gophr\Resources\Job;
use Shimango\Gophr\Resources\Parcel;

class ResourceFactory
{
    /**
     * Makes a Job resource
     * @param Configuration $configuration
     * @return Job
     * @throws GophrException
     */
    public function makeJobResource(Configuration $configuration): Job
    {
        $gophrRequest = RequestFactory::makeGophrRequest($configuration);
        return new Job($gophrRequest);
    }

    /**
     *  Makes a Delivery resource
     * @param Configuration $configuration
     * @return Delivery
     * @throws GophrException
     */
    public function makeDeliveryResource(Configuration $configuration): Delivery
    {
        $gophrRequest = RequestFactory::makeGophrRequest($configuration);
        return new Delivery($gophrRequest);
    }

    /**
     * Makes a parcel resource
     * @param Configuration $configuration
     * @return Parcel
     * @throws GophrException
     */
    public function makeParcelResource(Configuration $configuration): Parcel
    {
        $gophrRequest = RequestFactory::makeGophrRequest($configuration);
        return new Parcel($gophrRequest);
    }
}
