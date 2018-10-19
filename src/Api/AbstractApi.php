<?php

namespace ZoomAPI\Api;

use ZoomAPI\ZoomAPIClient;

/**
 * Abstract class for Api classes.
 */
abstract class AbstractApi implements ApiInterface {

  /**
   * The Zoom API client.
   *
   * @var ZoomAPI\ZoomAPIClient
   */
  protected $client;

  /**
   * Constructor.
   */
  public function __construct(ZoomAPIClient $client) {
    $this->client = $client;
  }

  /**
   * GET.
   */
  public function get($path, array $params = []) {
    $path = $this->preparePath($path, $params);
    return $this->client->getHttpClient()->get($path);
  }

  /**
   * POST.
   */
  public function post($path, array $params = []) {
    var_dump($path, $params);
    return $this->client->getHttpClient()->post($path, $params);
  }

  /**
   * Pepare path encoding query.
   */
  private function preparePath($path, array $params = []) {
    if (count($params)) {
      $path .= '?' . http_build_query($params);
    }
    return $path;
  }

}
