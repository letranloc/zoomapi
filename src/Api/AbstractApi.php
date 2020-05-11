<?php

namespace ZoomAPI\Api;

use ZoomAPI\ZoomAPIClient;
use ZoomAPI\Exception\ApiException;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract class for API classes.
 *
 * @todo separate class for resolver / validation.
 */
abstract class AbstractApi implements ApiInterface {

  /**
   * @var string
   */
  protected $resourcePath;

  /**
   * @var string
   */
  protected $resourceReplaceId = '{id}';

  /**
   * @var array
   */
  protected $propertyDefs = [];

  /**
   * @var ZoomAPI\ZoomAPIClient
   */
  protected $client;

  /**
   * @var Http\Message\StreamFactory
   */
  private $streamFactory;

  /**
   * @param ZoomApi\ZoomAPIClient $client
   * @param Http\Message\StreamFactory|null $streamFactory
   */
  public function __construct(ZoomAPIClient $client, StreamFactory $streamFactory = NULL) {
    $this->client = $client;
    $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
  }

  /**
   * Performs a GET query and returns the response as a PSR-7 response object.
   *
   * @param string $path
   * @param array $params
   * @return Psr\Http\Message\ResponseInterface
   */
  protected function getAsResponse($path, array $params = []) {
    $path = $this->preparePath($path, $params);
    $response = $this->client->getHttpClient()->get($path);
    return $this->handleResponse($response);
  }

  /**
   * GET.
   *
   * @param string $path
   * @param array $params
   * @return mixed
   */
  protected function get($path, array $params = []) {
    $response = $this->getAsResponse($path, $params);
    return $this->getContent($response);
  }

  /**
   * POST.
   *
   * @param string $path
   * @param array $params
   * @return mixed
   */
  protected function post($path, array $params = []) {
    $path = $this->preparePath($path);
    $body = json_encode($params);
    $response = $this->handleResponse($this->client->getHttpClient()->post($path, [], $body));
    return $this->getContent($response);
  }

  /**
   * PATCH.
   *
   * @param string $path
   * @param array $params
   * @return mixed
   */
  protected function patch($path, array $params = []) {
    $path = $this->preparePath($path);
    $body = json_encode($params);
    $response = $this->handleResponse($this->client->getHttpClient()->patch($path, [], $body));
    return $this->getContent($response);
  }

  /**
   * PUT.
   *
   * @param string $path
   * @param array $params
   * @return mixed
   */
  protected function put($path, array $params = []) {
    $path = $this->preparePath($path);
    $body = json_encode($params);
    $response = $this->handleResponse($this->client->getHttpClient()->put($path, [], $body));
    return $this->getContent($response);
  }

  /**
   * DELETE.
   *
   * @param string $path
   * @param array $params
   * @return mixed
   */
  protected function delete($path, array $params = []) {
    $path = $this->preparePath($path);
    $body = json_encode($params);
    $response = $this->handleResponse($this->client->getHttpClient()->delete($path, [], $body));
    return $this->getContent($response);
  }

  /**
   * Handle Response.
   */
  protected function handleResponse(ResponseInterface $response) {
    $statusCode = $response->getStatusCode();

    if ($statusCode < 200 || $statusCode > 299) {
      $content = json_decode($response->getBody()->__toString());
      $zoomCode = !empty($content->code) ? $content->code : 0;
      $zoomMsg = !empty($content->message) ? $content->message : '';

      // @todo
      throw new ApiException(
        sprintf('[%d] Error handling response. [%d] (%s)', $statusCode, $zoomCode, $zoomMsg),
        $statusCode,
        NULL,
        $response->getBody()->__toString()
      );
    }

    return $response;
  }

  /**
   * Get Response Content.
   */
  protected function getContent(ResponseInterface $response) {
    $body = $response->getBody()->__toString();
    $content = json_decode($body, TRUE);
    return $content;
  }

  /**
   * Get resource path.
   */
  protected function getResourcePath($id = '', $replace = '', $subPath = '') {
    $replace = $replace ?: $this->resourceReplaceId;

    if (!strlen($id)) {
      $replace = '/' . $replace;
    }

    $path = str_replace($replace, $id, $this->resourcePath);

    if ($subPath) {
      $path .= '/' . trim($subPath, '/');
    }

    return $path;
  }

  /**
   * Get Property Definitions.
   */
  protected function getPropertyDefs($setName = '') {
    return $this->propertyDefs;
  }

  /**
   * Resolve properties.
   */
  protected function resolveOptions(array $params, array $propertyDefs) {
    $resolver = new OptionsResolver();
    $resolved = [];

    $this->preResolveOptions($params, $propertyDefs);

    foreach ($propertyDefs as $propertyName => $propertyInfo) {
      $resolver->setDefined($propertyName);

      if (!empty($propertyInfo['required'])) {
        $resolver->setRequired($propertyName);
      }

      if (!empty($propertyInfo['types'])) {
        $resolver->setAllowedTypes($propertyName, $propertyInfo['types']);
      }

      if (!empty($propertyInfo['values'])) {
        $resolver->setAllowedValues($propertyName, $propertyInfo['values']);
      }

      elseif (!empty($propertyInfo['constraints'])) {
         foreach ($propertyInfo['constraints'] as $constraintType => $constraintConditions) {
           $methodName = 'resolveOptionConstraint' . ucfirst(strtolower($constraintType));
           $resolver->setAllowedValues($propertyName, function ($value) use ($methodName, $constraintConditions) {
             return $this->$methodName($value, $constraintConditions);
           });
         }
      }

      if (!empty($propertyInfo['default'])) {
        $resolver->setDefault($propertyName, $propertyInfo['default']);
      }
    }

    $resolved = $resolver->resolve($params);

    foreach ($resolved as $property => $value) {
      if (!empty($propertyDefs[$property]['resolveSetName'])) {
        $resolved[$property] = $this->resolveOptionsBySet($value, $propertyDefs[$property]['resolveSetName']);
      }
      elseif (!empty($propertyDefs[$property]['propertyDefs'])) {
        $resolved[$property] = $this->resolveOptions($value, $propertyDefs[$property]['propertyDefs']);
      }
    }

    return $resolved;
  }

  /**
   * Manipulate options before resolving.
   */
  protected function preResolveOptions(array &$params, array $propertyDefs) {
    foreach ($propertyDefs as $property => $propertyDef) {
      if (!array_key_exists($property, $params)) {
        continue;
      }

      if ($propertyDef['types'] == 'int' || (is_array($propertyDef['types']) && in_array('int', $propertyDef['types'])) && is_numeric($params[$property])) {
        $params[$property] = (int) $params[$property];
      }
    }
  }

  /**
   * Resolve properties by set.
   */
  protected function resolveOptionsBySet(array $params, $propertySetName) {
    $propertyDefs = $this->getPropertyDefs($propertySetName);
    return $this->resolveOptions($params, $propertyDefs);
  }

  /**
   * Resolve constraint (minimum).
   */
  protected function resolveOptionConstraintMin($value, $min) {
    return ($value >= $min);
  }

  /**
   * Resolve constraint (maximum).
   */
  protected function resolveOptionConstraintMax($value, $max) {
    return ($value <= $max);
  }

  /**
   * Resolve constraint (range).
   */
  protected function resolveOptionConstraintRange($value, $range) {
    return ($value >= $range[0] && $value <= $range[1]);
  }

  /**
   * Resolve constraint (min length).
   */
  protected function resolveOptionConstraintMinlength($value, $min) {
    return (strlen($value) >= $min);
  }

  /**
   * Resolve constraint (max length).
   */
  protected function resolveOptionConstraintMaxlength($value, $max) {
    return (strlen($value) <= $max);
  }

  /**
   * Resolve constraint (range length).
   */
  protected function resolveOptionConstraintRangelength($value, $range) {
    return (strlen($value) >= $range[0] && strlen($value) <= $range[1]);
  }

  /**
   * Resolve constraint (email).
   */
  protected function resolveOptionConstraintEmail($value):bool {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  /**
   * Resolve constraint (regex).
   */
  protected function resolveOptionConstraintRegex($value, $pattern):bool {
    return preg_match_all($pattern, $value) === 0;
  }

  /**
   * Pepare path encoding query.
   */
  private function preparePath($path, array $params = []) {
    if (count($params)) {
      $path .= '?' . http_build_query($params);
    }
    return '/' . trim($path, '/');
  }

}
