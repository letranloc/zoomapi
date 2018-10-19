<?php

namespace ZoomAPI\Api;

/**
 * Zoom Users API.
 */
class Users extends AbstractApi {

  /**
   * List users (paginated).
   */
  public function list($status = '', $page_size = 0, $page_number = 0) {
    $params = array_filter([
      'status' => $status,
      'page_size' => $page_size,
      'page_number' => $page_number,
      // @todo testing only.
      // 'api_key' => 'cId5ywvrQkC3D9EDVyFpDA',
      // 'api_secret' => 'ENb88nx9phZQnzhpzOvo7NTZENhcTAbCRFHT',
    ]);
    return $this->post('https://api.zoom.us/v2/users', $params);
  }

}
