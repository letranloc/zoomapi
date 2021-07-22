<?php

namespace ZoomAPI\Api;

use ZoomAPI\Exception\InvalidArgumentException;

/**
 * Zoom Meetings API.
 */
class Meetings extends AbstractPagerApi {

  /**
   * {@inheritdoc}
   */
  protected $resourcePath = 'meetings';

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

  /**
   * Fetch list of user meetings.
   */
  public function fetchUserMeetings($userId, array $params = []) {
    $resourcePath = "users/{$userId}/meetings";
    $content = $this->fetchContent($params, $resourcePath);
    return $content[$this->getListKey()];
  }

  /**
   * {@inheritdoc}
   */
  public function fetch(array $params = []) {
    if (!empty($params['user_id'])) {
      $userId = $params['user_id'];
      unset($params['user_id']);
      return $this->fetchUserMeetings($userId, $params);
    }

    throw new InvalidArgumentException('A "user_id" is required to fetch a list of meetings.');
  }

}
