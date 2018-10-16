<?php

namespace PortalDNA\ZoomAPI;

// @todo
// use Http\Client\Exception as HttplugException;
// use Http\Discovery\StreamFactoryDiscovery;
// use Psr\Http\Message\RequestInterface;
// use Psr\Http\Message\ResponseInterface;
// use Wsc\Component\Todo\Exception\InvalidArgumentException;
// use Wsc\Component\Todo\Exception\NotFoundException;
// use Wsc\Component\Todo\Exception\UnkownErrorException;
// use Wsc\Component\Todo\Model\Todo;
// use Wsc\Component\Todo\Model\TodoCollection;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;

/**
 * Zoom API (v2) Client Library.
 *
 * @todo docblock comments.
 */
class ZoomAPIClient {

  /**
   * A properly configured HttpClient.
   *
   * @var Http\Client\HttpClient
   */
  private $httpClient;

  /**
   * The HTTP Message Factory.
   *
   * @var Http\Message\RequestFactory
   */
  private $requestFactory;

  /**
   * The HTTP StreamFactory.
   *
   * @var Http\Message\StreamFactory
   */
  private $streamFactory;

  private $apiKey = NULL;
  private $apiSecret = NULL;

  /**
   * ZoomAPIClient constructor.
   *
   * @param Http\Client\HttpClient|null $httpClient
   *   A properly configured HttpClient.
   * @param Http\Message\RequestFactory|null $requestFactory
   *   The HTTP Message Factory.
   * @param Http\Message\StreamFactory|null $streamFactory
   *   The HTTP StreamFactory.
   * @param string $apiKey
   *   The Zoom API Key.
   * @param string $apiSecret
   *   The Zoom API Secret.
   *
   * @see HttpClientFactory::createClient
   */
  public function __construct(HttpClient $httpClient = NULL, RequestFactory $requestFactory = NULL, StreamFactory $streamFactory = NULL, $apiKey, $apiSecret) {
    $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
    $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();

    $this->apiKey = $apiKey;
    $this->apiSecret = $apiSecret;
  }

  /**
   * Magic __call.
   */
  public function __call($method, $args) {
    return $this->make($method);
  }

  /**
   * Magic __get.
   */
  public function __get($name) {
    return $this->make($name);
  }

  /**
   * Make.
   */
  public function make($resource) {
    $class = 'PortalDNA\\ZoomAPI\\' . ucfirst(strtolower($resource));

    if (class_exists($class)) {
      // @todo return new $class($this->apiKey, $this->apiSecret);
      return new $class();
    }

    // @todo better handling?
    throw new Exception('Wrong method');
  }

}
