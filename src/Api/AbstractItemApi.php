<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract class for API item classes.
 */
abstract class AbstractItemApi extends AbstractApi {

  /**
   * Create Item.
   */
  public function create(array $params = []) {
    $params = $this->resolveOptionsBySet($params, 'create');
    return $this->post($this->getResourcePath(), $params);
  }

  /**
   * Fetch Item.
   */
  public function fetch($id, array $params = []) {
    $this->validateId($id);
    $params = $this->resolveOptionsBySet($params, 'fetch');
    $content = $this->get($this->getResourcePath($id), $params);
    return $content;
  }

  /**
   * Update Item.
   */
  public function update($id, array $params = []) {
    $this->validateId($id);
    $params = $this->resolveOptionsBySet($params, 'update');
    return $this->patch($this->getResourcePath($id), $params);
  }

  /**
   * Remove Item.
   */
  public function remove($id, array $params = []) {
    $this->validateId($id);
    $params = $this->resolveOptionsBySet($params, 'remove');
    return $this->delete($this->getResourcePath($id), $params);
  }

  /**
   * Validate Item ID.
   *
   * @todo this doesn't do anything at the moment. Throw expections if not valid.
   */
  protected function validateId($id) {
    return (is_string($id) || is_numeric($id));
  }

}
