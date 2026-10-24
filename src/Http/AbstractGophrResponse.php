<?php

namespace Shimango\Gophr\Http;

use Shimango\Gophr\DataTransferObjects\AbstractDataTransferObject;
use Shimango\Gophr\Interfaces\GophrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Spatie\DataTransferObject\DataTransferObjectError;

abstract class AbstractGophrResponse implements GophrResponseInterface
{
    private string $reasonPhrase = '';

    private string $protocolVersion = '1.1';

    protected ?AbstractDataTransferObject $object = null;

    protected array $contentsArray;

    /**
     * Creates a new Gophr HTTP response entity
     * @param StreamInterface $stream The body of the response
     * @param int $httpStatusCode The returned status code
     * @param array $headers The returned headers
     */
    public function __construct(private StreamInterface $stream, private int $httpStatusCode, private array $headers)
    {
    }

    /**
     * Gets the decoded body of the HTTP response
     * @return StreamInterface The decoded body
     */
    public function getBody(): StreamInterface
    {
        return $this->stream;
    }

    /**
     * Gets the status of the HTTP response
     * @return int The HTTP status
     */
    public function getStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    /**
     * Gets the headers of the response
     * @return array The response headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): GophrResponseInterface
    {
        $new = clone $this;
        $new->protocolVersion = $version;
        return $new;
    }

    public function hasHeader(string $name): bool
    {
        return $this->findHeaderName($name) !== null;
    }

    public function getHeader(string $name): array
    {
        $headerName = $this->findHeaderName($name);

        if ($headerName === null) {
            return [];
        }

        $value = $this->headers[$headerName];
        return is_array($value) === true ? $value : [$value];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->getHeader($name));
    }

    public function withHeader(string $name, string|array $value): GophrResponseInterface
    {
        $new = $this->withoutHeader($name);
        $new->headers[$name] = $value;
        return $new;
    }

    public function withAddedHeader(string $name, string|array $value): GophrResponseInterface
    {
        $headerName = $this->findHeaderName($name) ?? $name;
        $addedValues = is_array($value) === true ? $value : [$value];

        $new = clone $this;
        $new->headers[$headerName] = array_merge($this->getHeader($name), $addedValues);
        return $new;
    }

    public function withoutHeader(string $name): GophrResponseInterface
    {
        $new = clone $this;

        $headerName = $this->findHeaderName($name);
        if ($headerName !== null) {
            unset($new->headers[$headerName]);
        }

        return $new;
    }

    private function findHeaderName(string $name): ?string
    {
        foreach (array_keys($this->headers) as $headerName) {
            if (strcasecmp((string)$headerName, $name) === 0) {
                return $headerName;
            }
        }

        return null;
    }

    public function withBody(StreamInterface $stream): GophrResponseInterface
    {
        $new = clone $this;
        $new->stream = $stream;
        return $new;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): GophrResponseInterface
    {
        $new = clone $this;
        $new->httpStatusCode = $code;
        $new->reasonPhrase = $reasonPhrase;
        return $new;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function getContentsArray(): array
    {
        if (!isset($this->contentsArray)) {
            $contents = json_decode($this->stream->getContents(), true);
            $this->contentsArray = is_array($contents) === true ? $contents : [];
        }

        return $this->contentsArray;
    }

    protected function getDataTransferObject(string $className): ?AbstractDataTransferObject
    {
        if (!isset($this->object)) {
            try {
                $this->object = new $className($this->getContentsArray());
            } catch (DataTransferObjectError) {
                $this->object = null;
            }
        }

        return $this->object;
    }
}
