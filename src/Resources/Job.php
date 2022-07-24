<?php

namespace Shimango\Gophr\Resources;

use Shimango\Gophr\Exceptions\InvalidReturnTypeException;
use Shimango\Gophr\Http\Responses\Jobs\CancelJobResponse;
use Shimango\Gophr\Http\Responses\Jobs\CreateJobResponse;
use Shimango\Gophr\Http\Responses\Jobs\GetJobResponse;
use Shimango\Gophr\Http\Responses\Jobs\GetQuoteResponse;
use Shimango\Gophr\Http\Responses\Jobs\ListJobsResponse;
use Shimango\Gophr\Http\Responses\Jobs\UpdateJobResponse;

/**
 * A job resource represents a collection of deliveries as well as the metadata associated with them.
 * A Job will have a pickup and many dropoffs.
 * @package Shimango\Gophr\Resources
 */
class Job extends AbstractResource
{
    /**
     * Creates a job
     * @param array $jobData
     * @return CreateJobResponse
     * @throws InvalidReturnTypeException
     */
    public function createJob(array $jobData): CreateJobResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs";
        /** @var CreateJobResponse $response */
        $response = $this->create($apiEndPoint, $jobData, CreateJobResponse::class);
        return $response;
    }

    /**
     * Gets a job
     * @param string $jobId
     * @return GetJobResponse
     * @throws InvalidReturnTypeException
     */
    public function getJob(string $jobId): GetJobResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}";
        /** @var GetJobResponse $response */
        $response = $this->read($apiEndPoint, GetJobResponse::class);
        return $response;
    }

    /**
     * Updates a job
     * @param string $jobId
     * @param array $jobData
     * @return UpdateJobResponse
     * @throws InvalidReturnTypeException
     */
    public function updateJob(string $jobId, array $jobData): UpdateJobResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}";
        /** @var UpdateJobResponse $response */
        $response = $this->update($apiEndPoint, $jobData, UpdateJobResponse::class);
        return $response;
    }

    /**
     * Cancels a job
     * @param string $jobId
     * @param array $jobData
     * @return CancelJobResponse
     * @throws InvalidReturnTypeException
     */
    public function CancelJob(string $jobId, array $jobData): CancelJobResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/jobs/{$jobId}/cancel";
        /** @var CancelJobResponse $response */
        $response = $this->create($apiEndPoint, $jobData, CancelJobResponse::class);
        return $response;
    }

    /**
     * Lists jobs
     * @param int $page
     * @param int $count
     * @param bool $include_finished
     * @return ListJobsResponse
     * @throws InvalidReturnTypeException
     */
    public function listJobs(int $page = 1 ,int $count = 15, bool $include_finished = false): ListJobsResponse
    {
        $include_finished = (int)$include_finished;
        $apiEndPoint = $this->request->getEndpoint() . "/jobs?page={$page}&count={$count}&include-finished={$include_finished}";
        /** @var ListJobsResponse $response */
        $response = $this->read($apiEndPoint, ListJobsResponse::class);
        return $response;
    }

    /**
     * Gets a quote
     * @param array $jobData
     * @return GetQuoteResponse
     * @throws InvalidReturnTypeException
     */
    public function getQuote(array $jobData): GetQuoteResponse
    {
        $apiEndPoint = $this->request->getEndpoint() . "/quotes";
        /** @var GetQuoteResponse $response */
        $response = $this->create($apiEndPoint, $jobData, GetQuoteResponse::class);
        return $response;
    }
}
