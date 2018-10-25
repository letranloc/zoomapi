<?php

namespace ZoomAPI\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;

/**
 * A builder that builds the API client.
 *
 * This will allow you to fluently add and remove plugins.
 */
class Builder {

  /**
   * The object that sends HTTP messages.
   *
   * @var Http\Client\HttpClient
   */
  private $httpClient;

  /**
   * A HTTP client with all our plugins.
   *
   * @var Http\Client\Common\HttpMethodsClient
   */
  private $pluginClient;

  /**
   * The HTTP Request (or Message) Factory.
   *
   * @var Http\Message\RequestFactory
   */
  private $requestFactory;

  /**
   * The HTTP Message Stream Factory.
   *
   * @var Http\MessageStreamFactory
   *
   * @todo is this needed?
   */
  private $streamFactory;

  /**
   * True if we should create a new Plugin client at next request.
   *
   * @var bool
   */
  private $httpClientModified = TRUE;

  /**
   * An array of Http Client Plugins.
   *
   * @var Http\Client\Common\Plugin[]
   */
  private $plugins = [];

  /**
   * The Builder construct method.
   *
   * @param Http\Client\HttpClient $httpClient
   *   The Http Client.
   * @param Http\Message\RequestFactory $requestFactory
   *   The Request Factory.
   * @param Http\Message\StreamFactory $streamFactory
   *   The Stream Factory.
   */
  public function __construct(HttpClient $httpClient = NULL, RequestFactory $requestFactory = NULL, StreamFactory $streamFactory = NULL) {
    $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
    $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
    $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
  }

  /**
   * Recreate and retrieve the HttpMethodsClient.
   *
   * @return Http\Client\Common\HttpMethodsClient
   *   The HttpMethodsClient.
   */
  public function getHttpClient() {
    if ($this->httpClientModified) {
      $this->httpClientModified = FALSE;

      $this->pluginClient = new HttpMethodsClient(
        (new PluginClientFactory())->createClient($this->httpClient, $this->plugins),
        $this->requestFactory
      );
    }
    return $this->pluginClient;
  }

  /**
   * Add a new plugin to the end of the plugin chain.
   *
   * @param Http\Client\Common\Plugin $plugin
   *   An Http Client Plugin.
   */
  public function addPlugin(Plugin $plugin) {
    $this->plugins[] = $plugin;
    $this->httpClientModified = TRUE;
  }

  /**
   * Remove a plugin by its fully qualified class name (FQCN).
   *
   * @param string $fqcn
   *   The fully qualified class name.
   */
  public function removePlugin($fqcn) {
    foreach ($this->plugins as $idx => $plugin) {
      if ($plugin instanceof $fqcn) {
        unset($this->plugins[$idx]);
        $this->httpClientModified = TRUE;
      }
    }
  }

}
