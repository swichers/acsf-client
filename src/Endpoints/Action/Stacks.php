<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * @Action(name = "Stacks")
 */
class Stacks extends ActionBase {

  /**
   * Fetches the list of available stacks.
   *
   * @version v1
   * @title Get list of available stacks.
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/stacks
   * @body
   * @example_command
   *   curl '{base_url}/api/v1/stacks' \
   *     -v -u {user_name}:{api_key} -X GET
   * @example_response
   *   {
   *     "stacks": {
   *       "1": "abcde",
   *       "2": "fghij"
   *     }
   *   }
   */
  public function list() : array {
    static $stacks;
    if (is_null($stacks)) {
      $stacks = $this->client->apiGet('stacks')->toArray();
    }
    return $stacks;
  }

}
