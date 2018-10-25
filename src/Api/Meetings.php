<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Zoom Meetings API.
 */
class Meetings extends AbstractApi {

  /**
   * {@inheritdoc}
   */
  protected $resourcePath = 'users/{$userId}/meetings';

  /**
   * {@inheritdoc}
   */
  protected $resourceReplaceId = '{$userId}';

  /**
   * {@inheritdoc}
   */
  protected $propertyDefs = [
    'type' => [
      'types' => ['string'],
      'values' => ['scheduled', 'live', 'upcoming'],
      'default' => 'live',
    ],
  ];

}
