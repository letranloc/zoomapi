<?php

namespace PortalDNA\ZoomAPI;

// @todo
// use Http\Client\Common\Plugin\AuthenticationPlugin;
// use Http\Message\Authentication;
// use Http\Message\MessageFactory;
// use Http\Message\StreamFactory;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Psr\Http\Message\UriInterface;

/**
 * Http Client Factory for use with HTTPlug.
 */
class HttpClientFactory {

  /**
   * Wrap client with the necessary behaviour to work with the API.
   *
   * @param Psr\Http\Message\UriInterface|string $host
   *   URL of the API host.
   * @param array $plugins
   *   Additional plugins for the HTTP client.
   * @param Http\Client\HttpClient|null $httpClient
   *   Basic client instance if you don't want to use discovery.
   *
   * @return Http\Client\Common\PluginClient
   *   Returns a Client Plugin.
   */
  public static function createClient($host, array $plugins = [], HttpClient $httpClient = NULL) {
    if (is_string($host)) {
      $host = UriFactoryDiscovery::find()->createUri($host);
    }
    if (!$host instanceof UriInterface) {
      throw new \InvalidArgumentException('server uri must be string or a PSR-7 UriInterface');
    }
    if (!$host->getHost()) {
      throw new \InvalidArgumentException('server uri must specify the host: "' . $host . '""');
    }

    $plugins[] = new AddHostPlugin($host);

    if (!$httpClient) {
      $httpClient = HttpClientDiscovery::find();
    }

    return new PluginClient($httpClient, $plugins);
  }

}
