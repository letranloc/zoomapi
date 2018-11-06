<?php

namespace ZoomAPI\Api;

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

  /**
   * Check if email exists.
   */
  public function emailExists($email) {
    $params = $this->resolveOptionsBySet(['email' => $email], 'email');
    $content = $this->get($this->getResourcePath() . '/email', $params);
    return !empty($content['existed_email']);
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefs($setName = '') {
    $defs = parent::getPropertyDefs($setName);
    $properties = [];

    switch ($setName) {
      case 'fetch':
        $properties = ['page_size', 'page_number', 'status'];
        break;

      case 'email':
        $properties = ['email'];
        $defs = [
          'email' => [
            'required' => TRUE,
            'types' => 'string',
            'constraints' => [
              'maxlength' => 128,
              'email' => TRUE,
            ],
          ],
        ] + $defs;
        break;
    }

    $defs = array_intersect_key($defs, array_combine($properties, $properties));
    return $defs;
  }

}
