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
    $resourcePath = "users/{$userId}/recordings";
    unset($params['user_id']);
    $params = $this->resolveOptionsBySet($params, 'fetchUserMeetingRecordings');
    return $this->get($resourcePath, $params);
  }

  /**
   * Fetch all recordings for a meeting.
   */
  public function fetchMeetingRecordings($meetingId, array $params = []) {
    $resourcePath = "meetings/{$meetingId}/recordings";
    unset($params['meeting_id']);
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
      $content = $this->fetchUserMeetingRecordings($params['user_id'], $params);
      return $content['meetings'];
    }

    throw new InvalidArgumentException('A "user_id" or "meeting_id" is required to fetch a list of recordings.');
  }

  /**
   * {@inheritdoc}
   */
  public function fetchAll(array $params = []) {
    if (!empty($params['meeting_id'])) {
      return $this->fetch($params);
    }
    elseif (!empty($params['user_id'])) {
      $params['from'] = date('Y-m-d', strtotime('-120 days'));
      $params['to'] = date('Y-m-d', time());
      $params['page_size'] = 300;
      $pageNum = 1;
      $items = [];

      do {
        $content = $this->fetchUserMeetingRecordings($params['user_id'], $params);

        // This is a work-around for inconsistent API responses. When looking
        // for meeting recordings an error code will be given when recordings
        // are not found, however when looking for user recordings, a 200 HTTP
        // code is provided but total records = 0 and the response is missing
        // the 'meetings' element.
        if (!empty($content['meetings'])) {
          $items = array_merge($items, $content['meetings']);
          $pageNum++;
          $params['next_page_token'] = $content['next_page_token'];
        }
      } while (!empty($content['next_page_token']) && $pageNum <= $content['page_count']);

      return $items;
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
          'default' => date('Y-m-d', strtotime('-30 days')),
        ];
        $defs['to'] = [
          'types' => 'string',
          'required' => TRUE,
          'normalizer' => 'datetime',
          'default' => date('Y-m-d', time()),
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
        $properties = [
          'from',
          'to',
          'page_size',
          'next_page_token',
          'mc',
          'trash',
        ];
        break;
    }

    $defs = array_intersect_key($defs, array_combine($properties, $properties));
    return $defs;
  }

}
