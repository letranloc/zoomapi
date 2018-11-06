<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Zoom Webhooks API.
 */
class Webhooks extends AbstractPagerApi {

  /**
   * {@inheritdoc}
   */
  protected $resourcePath = 'webhooks';

  /**
   * Set webhooks options.
   */
  public function options(array $params = []) {
    $defs = [
      'version' => [
        'types' => 'string',
        'required' => TRUE,
        'values' => ['v1', 'v2'],
      ],
    ];
    $params = $this->resolveOptions($params, $defs);
    return $this->patch($this->getResourcePath() . '/options', $params);
  }

}
