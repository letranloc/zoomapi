<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Zoom Webhook API.
 */
class Webhook extends AbstractItemApi {

  /**
   * {@inheritdoc}
   */
  protected $resourcePath = 'webhooks/{id}';

  /**
   * {@inheritdoc}
   */
  protected $propertyDefs = [
    'url' => [
      'types' => 'string',
      'constraints' => [
        'maxlength' => 256,
      ],
    ],
    'auth_user' => [
      'types' => 'string',
      'constraints' => [
        'maxlength' => 128,
      ],
    ],
    'auth_password' => [
      'types' => 'string',
      'constraints' => [
        'maxlength' => 64,
      ],
    ],
    'events' => [
      'types' => 'array',
      'values' => [
        'meeting_started',
        'meeting_ended',
        'meeting_jbh',
        'meeting_join',
        'recording_completed',
        'participant_joined',
        'participant_left',
        'meeting_registered',
        'recording_transcript_completed',
      ],
    ],
  ];

  /**
   * {@inheritdoc}
   */
  protected function getPropertyDefs($setName = '') {
    $defs = parent::getPropertyDefs($setName);
    $properties = [];

    switch ($setName) {
      case 'create':
      case 'update':
        $properties = [
          'url',
          'auth_user',
          'auth_password',
          'events',
        ];
        break;
    }

    $defs = array_intersect_key($defs, array_combine($properties, $properties));

    if ($setName == 'create') {
      foreach ($defs as $propery => $def) {
        $defs[$propery]['required'] = TRUE;
      }
    }

    return $defs;
  }

}
