<?php

namespace Shimango\Gophr\Factories;

use Shimango\Gophr\Http\AbstractGophrResponse;
use Psr\Http\Message\StreamInterface;

class ResponseFactory
{
    /**
     * Makes a Gophr request
     * @param StreamInterface $body
     * @param int $httpStatusCode
     * @param array $headers
     * @param string $dtoReturnType
     * @return AbstractGophrResponse
     */
    public static function makeGophrResponse(StreamInterface $body, int $httpStatusCode, array $headers, string $dtoReturnType): AbstractGophrResponse
    {
            return new $dtoReturnType($body, $httpStatusCode, $headers);
    }
}
