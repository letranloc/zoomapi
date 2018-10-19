<?php

namespace ZoomAPI;

use ZoomAPI\Api\AbstractApi;
use ZoomAPI\HttpClient\Builder;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\Plugin\AuthenticationPlugin;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Message\Authentication\Bearer;
use Http\Discovery\UriFactoryDiscovery;
use Firebase\JWT\JWT;

/**
 * Simple API wrapper for ZoomAPI.
 *
 * @todo add property-read statements.
 */
class ZoomAPIClient {

  /**
   * The Zoom API Url.
   *
   * @var string
   */
  private $apiUrl = 'https://api.zoom.us/v2';

  /**
   * The Zoom API private key.
   *
   * @var string
   */
  private $apiKey;

  /**
   * The Zoom API secret.
   *
   * @var string
   */
  private $apiSecret;

  /**
   * The Http Client Builder.
   *
   * @var ZoomAPI\HttpClient\Builder
   */
  private $httpClientBuilder;

  /**
   * The Zoom API constructor.
   *
   * @param string $apiKey
   *   The Zoom API private key.
   * @param string $apiSecret
   *   The Zoom API secret.
   * @param ZoomAPI\HttpClient\Builder|null $httpClientBuilder
   *   The Http Client Builder.
   */
  public function __construct($apiKey, $apiSecret, Builder $httpClientBuilder = NULL) {
    $this->setApiKey($apiKey);
    $this->setApiSecret($apiSecret);
    $this->httpClientBuilder = $httpClientBuilder ?: new Builder();

    $this->httpClientBuilder->addPlugin(new HeaderDefaultsPlugin([
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
    ]));

    $this->httpClientBuilder->addPlugin(new AuthenticationPlugin(new Bearer($this->generateJwt())));

    $this->setUrl($this->apiUrl);
  }

  /**
   * Users API.
   *
   * @return ZoomAPI\Api\Users
   *   The Users API.
   */
  public function users() {
    return new Api\Users($this);
  }

  /**
   * Make API calls.
   *
   * @param string $name
   *   The API name/class.
   *
   * @return ZoomAPI\AbstractApi|mixed
   *   Returns the resulting api/response.
   */
  public function api($name) {
    switch ($name) {

      case 'users':
        return $this->users();

      default:
        throw new InvalidArgumentException('Invalid endpoint: "' . $name . '"');
    }
  }

  /**
   * Set the Zoom API key.
   *
   * @param string $apiKey
   *   The Zoom API key.
   */
  public function setApiKey($apiKey) {
    // @todo validation.
    $this->apiKey = $apiKey;
  }

  /**
   * Set the Zoom API secret.
   *
   * @param string $apiSecret
   *   The Zoom API secret.
   */
  public function setApiSecret($apiSecret) {
    // @todo validation.
    $this->apiSecret = $apiSecret;
  }

  /**
   * Add the host plugin based on the provided Url.
   *
   * @param string $url
   *   The Zoom API base url.
   */
  public function setUrl($url) {
    $this->httpClientBuilder->removePlugin(AddHostPlugin::class);
    $this->httpClientBuilder->addPlugin(new AddHostPlugin(UriFactoryDiscovery::find()->createUri($url)));
    return $this;
  }

  /**
   * Get the Http Client.
   *
   * @return Http\Client\HttpClient
   *   Returns the builder HttpClient.
   */
  public function getHttpClient() {
    return $this->httpClientBuilder->getHttpClient();
  }

  /**
   * Generate JSON Web Token.
   */
  public function generateJwt() {
    $token = [
      'iss' => $this->apiKey,
      // @todo allow for changing expiration.
      'exp' => time() + 60,
    ];
    return JWT::encode($token, $this->apiSecret);
  }

}
