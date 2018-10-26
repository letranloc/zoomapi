<?php

namespace ZoomAPI\Model;

// @todo
// use ZoomAPI\Exception\InvalidArgumentException;
use ZoomAPI\ZoomAPIClient;

/**
 * Abstract Model.
 *
 * @todo move validation to separate or 3rd party class?
 */
abstract class AbstractModel implements ModelInterface {

  protected static $properties;
  protected $data = [];
  protected $client;

  /**
   * Constructor.
   */
  public function __construct(ZoomAPIClient $client, $id = NULL) {
    $this->client = $client;
    $this->id = $id;
  }

  /**
   * Get Unique ID.
   */
  public function getId() {
    return !empty($this->data['id']) ? $this->data['id'] : NULL;
  }

  /**
   * Magic Set.
   *
   * Map the setting of non-existing fields to a mutator when possible,
   * otherwise use the matching field.
   */
  public function __set($property, $value) {
    // @todo
    // if (!in_array($property, static::$properties)) {
    //   throw new InvalidArgumentException("Setting the property '{$property}' is not valid for this entity.");
    // }

    $mutator = $this->magicPropName($property, 'set');

    if (method_exists($this, $mutator) && is_callable([$this, $mutator])) {
      $this->$mutator($value);
    }
    else {
      $this->data[$property] = $value;
    }

    return $this;
  }

  /**
   * Magic Get.
   *
   * Map the getting of non-existing properties to an accessor when
   * possible, otherwise use the matching field.
   */
  public function __get($property) {
    // @todo
    // if (!in_array($property, static::$properties)) {
    //   throw new InvalidArgumentException("Getting the property '{$property}' is not valid for this entity.");
    // }

    $accessor = $this->magicPropName($property, 'get');

    return (method_exists($this, $accessor) && is_callable([$this, $accessor])) ? $this->$accessor() : $this->data[$property];
  }

  /**
   * Get the entity fields.
   */
  public function toArray() {
    return $this->data;
  }

  /**
   * Populate data.
   */
  public function fromArray($data) {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    }
    return $this;
  }

  /**
   * Get property setter/getter name.
   */
  private function magicPropName($property, $prefix) {
    return $prefix . str_replace('_', '', ucwords($property, '_'));
  }

}
