<?php

namespace ZoomAPI\Api;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Zoom Users API.
 */
class User extends AbstractItemApi {

  /**
   * {@inheritdoc}
   */
  protected $resourcePath = 'users/{id}';

  /**
   * {@inheritdoc}
   */
  protected $propertyDefs = [
    // This is the create user action. There are other action properties for
    // different api calls.
    'action' => [
      'required' => TRUE,
      'types' => 'string',
      'values' => ['create', 'autoCreate', 'custCreate', 'ssoCreate'],
      'default' => 'create',
    ],
    'user_info' => [
      'required' => TRUE,
      'types' => 'array',
      'resolveSetName' => 'user_info',
    ],
    'email' => [
      'required' => TRUE,
      'types' => 'string',
      'constraints' => [
        'maxlength' => 128,
        'email' => TRUE,
      ],
    ],
    // Required for create user but not for update.
    'type' => [
      'types' => 'int',
      'values' => [1, 2, 3],
    ],
    'first_name' => [
      'types' => 'string',
      'constaints' => [
        'maxlength' => 64,
      ],
    ],
    'last_name' => [
      'types' => 'string',
      'constaints' => [
        'maxlength' => 64,
      ],
    ],
    // @todo only for autoCreate.
    'password' => [
      'types' => 'string',
      'constraints' => [
        'maxlength' => 32,
      ],
    ],
    'pmi' => [
      'types' => 'string',
      'constraints' => [
        'rangelength' => [10, 10],
      ],
    ],
    // @todo constraint/values to timezone list.
    'timezone' => [
      'types' => 'string',
    ],
    'dept' => [
      'types' => 'string',
    ],
    'vanity_name' => [
      'types' => 'string',
    ],
    'host_key' => [
      'types' => 'string',
      'constraints' => [
        'rangelength' => [6, 6],
      ],
    ],
    'cms_user_id' => [
      'types' => 'string',
    ],
    'pic_file' => [
      'types' => 'string',
      'required' => TRUE,
    ],
    'login_type' => [
      'types' => 'string',
      'values' => [0, 1, 99, 100, 101],
    ],
    // User Settings.
    'schedule_meeting' => [
      'types' => 'array',
      'properties' => [
        'host_video' => [
          'types' => 'bool',
        ],
        'participants_video' => [
          'types' => 'bool',
        ],
        'audio_type' => [
          'types' => 'string',
          'values' => ['both', 'telephony', 'voip', 'thirdParty'],
          'default' => 'voip',
        ],
        'join_before_host' => [
          'types' => 'bool',
        ],
        'force_pmi_jbh_password' => [
          'types' => 'bool',
        ],
        'pstn_password_protected' => [
          'types' => 'bool',
        ],
      ],
    ],
    'in_meeting' => [
      'types' => 'array',
      'properties' => [
        'e2e_encryption' => [
          'types' => 'bool',
        ],
        'chat' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'private_chat' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'auto_saving_chat' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'entry_exit_chime' => [
          'types' => 'string',
          'values' => ['host', 'all', 'none'],
          'default' => 'all',
        ],
        'record_play_voice' => [
          'types' => 'bool',
        ],
        'file_transfer' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'feedback' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'co_host' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'polling' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'attendee_on_hold' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'annotation' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'remote_control' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'non_verbal_feedback' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'breakout_room' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'remote_support' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'closed_caption' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'group_hd' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'virtual_background' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'far_end_camera_control' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'share_dual_camera' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'attention_tracking' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'waiting_room' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'allow_live_streaming' => [
          'types' => 'bool',
        ],
        'workplace_by_facebook' => [
          'types' => 'bool',
        ],
        'custom_live_streaming' => [
          'types' => 'bool',
        ],
        'custom_service_instructions' => [
          'types' => 'string',
        ],
      ],
    ],
    'email_notification' => [
      'types' => 'array',
      'properties' => [
        'jbh_reminder' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'cancel_meeting_reminder' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'alternative_host_reminder' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
      ],
    ],
    'recording' => [
      'types' => 'array',
      'properties' => [
        'local_recording' => [
          'types' => 'bool',
        ],
        'cloud_recording' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'record_speaker_view' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'record_gallery_view' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'record_audio_file' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'save_chat_text' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'show_timestamp' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'recording_audio_transcript' => [
          'types' => 'bool',
        ],
        'auto_recording' => [
          'types' => 'string',
          'values' => ['local', 'cloud', 'none'],
          'default' => 'local',
        ],
        'auto_delete_cmr' => [
          'types' => 'bool',
          'default' => FALSE,
        ],
        'auto_delete_cmr_days' => [
          'types' => 'int',
          'constraints' => [
            'range' => [1, 60],
          ],
        ],
      ],
    ],
    'telephony' => [
      'types' => 'array',
      'properties' => [
        'third_party_audio' => [
          'types' => 'bool',
        ],
        'audio_conference_info' => [
          'types' => 'string',
          'constraints' => [
            'maxlength' => 2048,
          ],
        ],
        'show_international_numbers_link' => [
          'types' => 'bool',
        ],
      ],
    ],
    'feature' => [
      'types' => 'array',
      'properties' => [
        'meeting_capacity' => [
          'types' => 'int',
        ],
        'large_meeting' => [
          'types' => 'bool',
        ],
        'large_meeting_capacity' => [
          'types' => 'int',
          'values' => [100, 200, 300, 500],
        ],
        'webinar' => [
          'types' => 'bool',
        ],
        'webinar_capacity' => [
          'types' => 'int',
          'values' => [100, 500, 1000, 3000, 5000, 10000],
        ],
      ],
    ],
  ];

  /**
   * Set user picture.
   */
  public function picture($userId, array $params = []) {
    $this->validateId($userId);
    // @todo https://php-http.readthedocs.io/en/latest/components/multipart-stream-builder.html
    $params = $this->resolveOptionsBySet($params, 'picture');
  }

  /**
   * Retrieve a user's settings.
   */
  public function fetchSettings($userId, array $params = []) {
    $this->validateId($userId);
    $params = $this->resolveOptionsBySet($params, 'fetchSettings');
    return $this->get($this->getResourcePath($userId) . '/settings', $params);
  }

  /**
   * Update a user's settings.
   */
  public function updateSettings($userId, array $params) {
    $this->validateId($userId);
    $params = $this->resolveOptionsBySet($params, 'updateSettings');
    return $this->patch($this->getResourcePath($userId) . '/settings', $params);
  }

  /**
   * Fetch / update a user's settings.
   */
  public function settings($userId, array $params = []) {
    $this->validateId($userId);
    return $params ? $this->updateSettings($userId, $params) : $this->fetchSettings($userId);
  }

  /**
   * Update a user's status.
   */
  public function status($userId, $action) {
    $this->validateId($userId);
    $params = ['action' => $action];
    $params = $this->resolveOptionsBySet($params, 'status');
    return $this->put($this->getResourcePath($userId) . '/status', $params);
  }

  /**
   * Update a user's password.
   */
  public function password($userId, $password) {
    $this->validateId($userId);
    $params = ['password' => $password];
    $params = $this->resolveOptionsBySet($params, 'password');
    return $this->put($this->getResourcePath($userId) . '/password', $params);
  }

  /**
   * Update a user's email.
   */
  public function email($userId, $email) {
    $this->validateId($userId);
    $params = ['email' => $email];
    $params = $this->resolveOptionsBySet($params, 'email');
    return $this->put($this->getResourcePath($userId) . '/email', $params);
  }

  /**
   * Delete a user account.
   */
  public function deleteUser($userId, $params) {
    $this->validateId($userId);
    $params['action'] = 'delete';
    $params = $this->resolveOptionsBySet($params, 'remove');
    return $this->delete($this->getResourcePath($userId), $params);
  }

  /**
   * Disassociate a user account.
   */
  public function disassociateUser($userId, array $params = []) {
    $this->validateId($userId);
    $params['action'] = 'disassociate';
    $params = $this->resolveOptionsBySet($params, 'remove');
    return $this->delete($this->getResourcePath($userId), $params);
  }

  /**
   * {@inheritdoc}
   */
  protected function getPropertyDefs($setName = '') {
    $defs = parent::getPropertyDefs($setName);
    $properties = [];

    switch ($setName) {
      case 'create':
        $properties = ['action', 'user_info'];
        break;

      case 'user_info':
        $defs['type']['required'] = TRUE;
        $properties = [
          'email',
          'type',
          'first_name',
          'last_name',
          'password',
        ];
        break;

      case 'fetch':
        $properties = ['login_type'];
        break;

      case 'update':
        $properties = [
          'first_name',
          'last_name',
          'type',
          'pmi',
          'timezone',
          'dept',
          'vanity_name',
          'host_key',
          'cms_user_id',
        ];
        break;

      case 'email':
        $properties = ['email'];
        break;

      case 'remove':
        $defs['action']['values'] = ['delete', 'disassociate'];
        $defs['action']['default'] = 'disassociate';
        $properties = [
          'action',
          'transfer_email',
          'transfer_meeting',
          'transfer_webinar',
          'transfer_recording',
        ];
        break;

      case 'picture':
        $properties = ['pic_file'];
        break;

      case 'status':
        $defs['action'] = [
          'types' => 'string',
          'values' => ['activate', 'deactivate'],
          'required' => TRUE,
        ];
        $properties = ['action'];
        break;

      case 'fetchSettings':
        $properties = ['login_type'];
        break;

      case 'updateSettings':
        $properties = [
          'schedule_meeting',
          'in_meeting',
          'email_notification',
          'recording',
          'telephony',
          'feature',
        ];
        break;

    }

    $defs = array_intersect_key($defs, array_combine($properties, $properties));
    return $defs;
  }

}
