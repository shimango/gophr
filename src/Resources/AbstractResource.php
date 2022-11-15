<?php

namespace Shimango\Gophr\Resources;

use Shimango\Gophr\Exceptions\InvalidReturnTypeException;
use Shimango\Gophr\Http\AbstractGophrResponse;
use Shimango\Gophr\Http\GophrRequest;

/**
 * Abstract Resource
 * @package Shimango\Gophr\Resources
 */
abstract class AbstractResource
{
    protected GophrRequest $request;

    const REQUEST_GET = "GET";
    const REQUEST_POST = "POST";
    const REQUEST_PATCH = "PATCH";
    const REQUEST_DELETE = "DELETE";

    /**
     * @param GophrRequest $request
     */
    public function __construct(GophrRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Posts
     * @param string $endPoint
     * @param array $body
     * @param string|null $dtoResponseClass
     * @return AbstractGophrResponse
     * @throws InvalidReturnTypeException
     */
    public function create(string $endPoint, array $body, ?string $dtoResponseClass = null): AbstractGophrResponse
    {
        return $this->request->setEndpoint($endPoint)->attachBody($body)->setReturnType($dtoResponseClass)->execute(self::REQUEST_POST);
    }

    /**
     * Gets
     * @param string $endPoint
     * @param string|null $dtoResponseClass
     * @return AbstractGophrResponse
     * @throws InvalidReturnTypeException
     */
    public function read(string $endPoint, ?string $dtoResponseClass = null): AbstractGophrResponse
    {
        return $this->request->setEndpoint($endPoint)->setReturnType($dtoResponseClass)->execute(self::REQUEST_GET);
    }

    /**
     * Updates
     * @param string $endPoint
     * @param array $body
     * @param string|null $dtoResponseClass
     * @return AbstractGophrResponse
     * @throws InvalidReturnTypeException
     */
    public function update(string $endPoint, array $body, ?string $dtoResponseClass = null): AbstractGophrResponse
    {
        return $this->request->setEndpoint($endPoint)->attachBody($body)->setReturnType($dtoResponseClass)->execute(self::REQUEST_PATCH);
    }

    /**
     * Deletes
     * @param string $endPoint
     * @param string|null $dtoResponseClass
     * @return AbstractGophrResponse
     * @throws InvalidReturnTypeException
     */
    public function delete(string $endPoint, ?string $dtoResponseClass = null): AbstractGophrResponse
    {
        return $this->request->setEndpoint($endPoint)->setReturnType($dtoResponseClass)->execute(self::REQUEST_DELETE);
    }

    /**
     * Given a key value array, this function generates a URL-encoded query string from its contents.
     * @param array $parameters
     * @return string
     */
    protected function generateUrlParameters(array $parameters = []): string
    {
        if ($xdebugSession = getenv('GOPHR_XDEBUG_SESSION')) {
            $parameters['XDEBUG_SESSION'] = $xdebugSession;
        }

        return !empty($parameters) ? sprintf('?%s', http_build_query($parameters)) : '';
    }
}
