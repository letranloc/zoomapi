<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Zoom Users API.
 */
class Users extends AbstractPagerApi {

  /**
   * {@inheritdoc}
   */
  protected $resourcePath = 'users';

  /**
   * {@inheritdoc}
   */
  protected $propertyDefs = [
    'status' => [
      'required' => TRUE,
      'types' => ['string'],
      'values' => ['active', 'inactive', 'pending'],
      'default' => 'active',
    ],
  ];

}
