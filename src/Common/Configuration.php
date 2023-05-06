<?php
namespace Shimango\Gophr\Common;

/**
 * Configuration Class
  * @category Class
 * @package  Shimango\Gophr
 */
class Configuration
{
    private string $apiKey;
    private string $apiVersion;
    private bool $isSandbox;
    private ?int $proxyPort;
    private bool $proxyVerifySSL;

    private const API_VERSION = "v2";
    private const REST_BASE_URL = "https://api.gophr.com";
    private const REST_BASE_URL_SANDBOX = "https://api-sandbox.gophr.com";
    private const ENDPOINTS = [
        'v2' => '/v2-commercial-api',
    ];

    /**
     * Creates a configuration object
     * @param string $apiKey
     * @param bool $isSandbox
     * @param string $apiVersion
     * @param int|null $proxyPort
     * @param bool $proxyVerifySSL
     */
    public function __construct(string $apiKey, bool $isSandbox = false, string $apiVersion = self::API_VERSION, ?int $proxyPort = null, bool $proxyVerifySSL = false)
    {
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;
        $this->isSandbox = $isSandbox;
        $this->proxyPort = $proxyPort;
        $this->proxyVerifySSL = $proxyVerifySSL;
    }

    /**
     * Gets the API Key
     * @return string API token
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Sets the API Key
     * @return $this
     */
    public function setApiKey(string $apiKey): Configuration
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Gets the API version
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    /**
     * Sets the API version (v2)
     * @param string $apiVersion
     * @return Configuration
     */
    public function setApiVersion(string $apiVersion): Configuration
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    /**
     * Checks if sandbox mode is set
     * @return bool
     */
    public function isSandbox(): bool
    {
        return $this->isSandbox;
    }

    /**
     * Sets sandbox mode
     * @param bool $isSandbox
     * @return Configuration
     */
    public function setIsSandbox(bool $isSandbox): Configuration
    {
        $this->isSandbox = $isSandbox;
        return $this;
    }

    /**
     * Gets the proxy port
     * @return int|null
     */
    public function getProxyPort(): ?int
    {
        return $this->proxyPort;
    }

    /**
     * Sets the proxy port
     * @param int|null $proxyPort
     * @return Configuration
     */
    public function setProxyPort(?int $proxyPort): Configuration
    {
        $this->proxyPort = $proxyPort;
        return $this;
    }

    /**
     * Checks if proxy SSL verification is set
     * @return bool
     */
    public function isProxyVerifySSL(): bool
    {
        return $this->proxyVerifySSL;
    }

    /**
     * Sets proxy SSL verification
     * @param bool $proxyVerifySSL
     * @return Configuration
     */
    public function setProxyVerifySSL(bool $proxyVerifySSL): Configuration
    {
        $this->proxyVerifySSL = $proxyVerifySSL;
        return $this;
    }

    /**
     * Gets the base url
     * @return string
     */
    public function getBaseUrl(): string
    {
        if ($restBaseUrlDevelopment = getenv('REST_BASE_URL_DEVELOPMENT')) {
            return $restBaseUrlDevelopment;
        }

        return $this->isSandbox() ? self::REST_BASE_URL_SANDBOX : self::REST_BASE_URL;
    }

    /**
     * Gets the API endpoint based on the API version
     * @return string|null
     */
    public function getApiEndPoint(): ?string
    {
        return self::ENDPOINTS[$this->getApiVersion()] ?? null;
    }
}
