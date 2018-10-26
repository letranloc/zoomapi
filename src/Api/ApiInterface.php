<?php

namespace ZoomAPI\Api;

use ZoomApi\ZoomAPIClient;

/**
 * Zoom API Interface.
 */
interface ApiInterface {

  /**
   * ZoomAPI API Constructor.
   */
  public function __construct(ZoomAPIClient $client);

}
