<?php

namespace ZoomAPI\Api;

/**
 * Abstract class for API list classes.
 *
 * @todo use a pager class?
 */
abstract class AbstractPagerApi extends AbstractApi {

  /**
   * Fetch content.
   */
  public function fetchContent(array $params = [], $resourcePath = '') {
    $resourcePath = $resourcePath ?: $this->getResourcePath();
    $params = $this->resolveOptionsBySet($params, 'fetch');
    return $this->get($resourcePath, $params);
  }

  /**
   * Fetch list of items.
   */
  public function fetch(array $params = []) {
    $content = $this->fetchContent($params);
    $key = $this->getListKey();

    if (!isset($content[$key])) {
      throw new ApiException(sprintf('Error: Response content missing (%s) element.', $key));
    }

    return $content[$key];
  }

  /**
   * Fetch all items.
   */
  public function fetchAll(array $params = []) {
    $params['page_number'] = 1;
    $params['page_size'] = 300;
    $items = [];

    do {
      $content = $this->fetchContent($params);
      $items = array_merge($items, $content[$this->getListKey()]);
      $params['page_number']++;
    } while (!empty($content['page_count']) && $params['page_number'] <= $content['page_count']);

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
