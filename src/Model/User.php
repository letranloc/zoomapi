<?php

namespace ZoomAPI\Model;

/**
 * Class User.
 *
 * @see https://marketplace.zoom.us/docs/api-reference/zoom-api/models/user
 */
class User extends AbstractModel {

  const USER_TYPE_BASIC = 1;
  const USER_TYPE_PRO = 2;
  const USER_TYPE_CORP = 3;

  protected static $properties = [
    'id',
    'email',
    'first_name',
    'last_name',
    'type',
    'pmi',
    'timezone',
    'verified',
    'created_at',
    'last_login_time',
    'last_client_version',
    'use_pmi',
    'personal_meeting_url',
    'dept',
    'host_key',
    'group_ids',
    'im_group_ids',
    'account_id',
    'vanity_name',
    'cms_user_id',
    'user_settings',
    'meetings',
  ];

  /**
   * Retrieve the user.
   */
  public function fetch() {
    return $this->fromArray($this->client->api('user')->fetch($this->getId()));
  }

  /**
   * Create the user.
   */
  public function create($type, $email, array $data = []) {
    return $this->fromArray($this->client->api('user')->create($type, $email, $data));
  }

  /**
   * Custom Create the user.
   */
  public function custCreate($type, $email, array $data = []) {
    return $this->fromArray($this->client->api('user')->custCreate($type, $email, $data));
  }

  /**
   * Update user.
   */
  public function update($data) {
    $this->client->api('user')->update($this->getId(), $data);
    return $this;
  }

  /**
   * Delete user.
   */
  public function delete() {
    return $this->client->api('user')->delete($this->getId(), 'delete');
  }

  /**
   * Disassociate user.
   */
  public function disassociate(array $data = []) {
    return $this->client->api('user')->delete($this->getId(), 'disassociate');
  }

  /**
   * Get / update user settings.
   */
  public function settings(array $settings = []) {
    if (empty($settings)) {
      $content['user_settings'] = $this->client->api('user')->settings($this->getId());
      return $this->fromArray($content);
    }
    else {
      $this->client->api('user')->settings($this->getId(), $settings);
      return $this;
    }
  }

  /**
   * Update user status.
   */
  public function status($status) {
    $this->client->api('user')->status($this->getId(), $status);
    return $this;
  }

  /**
   * Activate user.
   */
  public function activate() {
    return $this->status('activate');
  }

  /**
   * Deactivate user.
   */
  public function deactivate() {
    return $this->status('deactivate');
  }

  /**
   * Update user password.
   */
  public function password($password) {
    $this->client->api('user')->password($this->getId(), $password);
    return $this;
  }

  /**
   * Update user email.
   */
  public function email($email) {
    $this->client->api('user')->email($this->getId(), $email);
    return $this;
  }

  public function meetings($type = 'live') {
    $content['meetings'] = $this->client->api('meetings')->listAll($this->getId(), $type);
    return $this->toArray($content);
  }

  public function allMeetings() {
    $content['meetings'] = [];

    foreach (['scheduled', 'live', 'upcoming'] as $type) {
      $content['meetings'] += $this->client->api('meetings')->listAll($this->getId(), $type);
    }

    return $this->toArray($content);
  }

}
