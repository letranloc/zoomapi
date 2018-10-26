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
   * @var int
   */
  protected $pageNumber;

  /**
   * @var int
   */
  protected $pageSize;

  /**
   * @var int
   */
  protected $pageCount;

  /**
   * @var int
   */
  protected $totalRecords;

  /**
   * @var array
   */
  protected $query = [];

  /**
   * @var mixed[]
   */
  protected $items = [];

  /**
   * Fetch page of items.
   */
  public function fetch(array $params = []) {
    $this->query = $this->resolveOptionsBySet($params, 'fetch');
    $content = $this->get($this->getResourcePath(), $this->query);
    $this->updatePager($content);
    return $this->items;
  }

  public function fetchAll(array $params = []) {
    $this->resetPager();
    $params['page_number'] = $this->pageNumber;
    $this->fetch($params);

    while (count($this->items) < $this->totalRecords && $this->pageNumber < ($this->totalRecords / $this->pageCount)) {
      $params = $this->query;
      $params['page_number']++;
      $this->fetch($params);
    }

    return $this->items;
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
   * Update paging properties.
   */
  protected function updatePager(array $content) {
    $this->pageNumber = $content['page_number'];
    $this->pageSize = $content['page_size'];
    $this->pageCount = $content['page_count'];
    $this->totalRecords = $content['total_records'];
    $this->items += $content[$this->getListKey()];
  }

  /**
   * Reset paging properties.
   */
  protected function resetPager() {
    $this->pageNumber = 1;
    $this->pageSize = 30;
    $this->pageCount = 0;
    $this->totalRecords = 0;
    $this->items = [];
  }

  /**
   * Get list key.
   */
  protected function getListKey() {
    return strtolower($this->getResourcePath());
  }

}
