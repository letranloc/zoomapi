<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Abstract class for API list classes.
 *
 * @todo use a pager class?
 */
abstract class AbstractPagerApi extends AbstractApi {

  /**
   * Fetch page of items.
   */
  public function fetchWithPageInfo(array $params = []) {
    $params = $this->resolveOptionsBySet($params, 'fetch');
    return $this->get($this->getResourcePath(), $params);
  }

  public function fetch(array $params = []) {
    $content = $this->fetchWithPageInfo($params);
    return $content[$this->getListKey()];
  }

  public function fetchAll(array $params = []) {
    $params['page_number'] = 1;
    $params['page_size'] = 300;
    $items = [];

    do {
      $content = $this->fetchWithPageInfo($params);
      $items = array_merge($items, $content[$this->getListKey()]);
      $params['page_number']++;
    } while ($params['page_number'] <= $content['page_count']);

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  protected function getPropertyDefs($setName = '') {
    return $this->propertyDefs + [
      'page_number' => [
        'required' => TRUE,
        'types' => 'int',
        'constraints' => [
          'min' => 1,
        ],
        'default' => 1,
      ],
      'page_size' => [
        'required' => TRUE,
        'types' => 'int',
        'constraints' => [
          'range' => [1, 300],
        ],
        'default' => 30,
      ],
    ];
  }

  /**
   * Get list key.
   */
  protected function getListKey() {
    return strtolower($this->getResourcePath());
  }

}
