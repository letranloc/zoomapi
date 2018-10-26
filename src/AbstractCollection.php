<?php

namespace ZoomAPI;

/**
 * Abstract collection.
 */
abstract class AbstractCollection implements \IteratorAggregate {

  protected $values = [];

  /**
   * Add Value.
   */
  public function addValue($value) {
    $key = $value->getId();

    if (isset($this->values[$key])) {
      throw new KeyHasUseExceptions("Key {$key} already in user.");
    }
    else {
      $this->values[$key] = $value;
    }
  }

  /**
   * Add Values.
   */
  public function addValues(array $values, $reset = FALSE) {
    if ($reset) {
      $this->delete();
    }

    foreach ($values as $value) {
      $this->addValue($value);
    }
  }

  /**
   * Get Value.
   */
  public function getValue($key) {
    if (isset($this->values[$key])) {
      return $this->values[$key];
    }
    else {
      throw new KeyInvalidException("Invalid key {$key}.");
    }
  }

  /**
   * Get keys.
   */
  public function keys() {
    return array_keys($this->values);
  }

  /**
   * Delete all.
   */
  public function delete() {
    $this->values = [];
  }

  /**
   * Key exists.
   */
  public function keyExists($key) {
    return isset($this->values[$key]);
  }

  /**
   * Values count.
   */
  public function count() {
    return count($this->values);
  }

  /**
   * Get array of values.
   */
  public function toArray(): array {
    $items = [];

    foreach ($this->values as $value) {
      $items[] = $value->toArray();
    }

    return $items;
  }

  /**
   * Get values iterator.
   */
  public function getIterator() {
    return new ArrayIterator($this->values);
  }

}
