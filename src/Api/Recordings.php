<?php

namespace ZoomAPI\Api;

use ZoomAPI\Exception\InvalidArgumentException;

/**
 * Zoom Recordings API.
 */
class Recordings extends AbstractPagerApi {

  /**
   * Fetch list of meeting recordings for a user.
   *
   * @todo this doesn't handle paging yet so filters need to restrict result
   * set size.
   */
  public function fetchUserMeetingRecordings($userId, array $params = []) {
    $params = $this->resolveOptionsBySet($params, 'fetchUserMeetingRecordings');
    $resourcePath = "users/{$userId}/recordings";
    $content = $this->fetchContent($params, $resourcePath);
    return $content;
  }

  /**
   * Fetch all recordings for a meeting.
   */
  public function fetchMeetingRecordings($meetingId, array $params = []) {
    $resourcePath = "meetings/{$meetingId}/recordings";
    $content = $this->fetchContent($params, $resourcePath);
    return $content;
  }

  /**
   * {@inheritdoc}
   */
  public function fetch(array $params = []) {
    if (!empty($params['meeting_id'])) {
      return $this->fetchMeetingRecordings($params['meeting_id'], $params);
    }
    elseif (!empty($params['user_id'])) {
      return $this->fetchUserMeetingRecordings($params['user_id'], $params);
    }

    throw new InvalidArgumentException('A "user_id" or "meeting_id" is required to fetch a list of recordings.');
  }

  /**
   * {@inheritdoc}
   */
  protected function getPropertyDefs($setName = '') {
    $defs = parent::getPropertyDefs($setName);
    $properties = [];

    switch ($setName) {
      case 'fetchUserMeetingRecordings':
        unset($defs['page_number']);
        // @todo validate date/time.
        $defs['from'] = [
          'types' => 'string',
          'required' => TRUE,
          'normalizer' => 'datetime',
        ];
        $defs['to'] = [
          'types' => 'string',
          'required' => TRUE,
          'normalizer' => 'datetime',
        ];
        $defs['next_page_token'] = [
          'types' => 'string',
        ];
        $defs['mc'] = [
          'types' => 'bool',
          'default' => FALSE,
        ];
        $defs['trash'] = [
          'types' => 'bool',
          'default' => FALSE,
        ];
        $properties = ['from', 'to', 'next_page_token', 'mc', 'trash'];
        break;
    }

    $defs = array_intersect_key($defs, array_combine($properties, $properties));
    return $defs;
  }

}
