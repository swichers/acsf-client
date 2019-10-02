<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

/**
 * ACSF Endpoint Wrapper: Stacks.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Stacks")
 */
class Stacks extends AbstractAction {

  /**
   * Fetches the list of available stacks.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of available stacks.
   *
   * @version v1
   * @title Get list of available stacks.
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/stacks
   * @example_response
   * ```json
   *   {
   *     "stacks": {
   *       "1": "abcde",
   *       "2": "fghij"
   *     }
   *   }
   * ```
   */
  public function list(array $options = []): array {

    static $stacks;
    if (is_null($stacks)) {
      $stacks = $this->client->apiGet('stacks')->toArray();
    }

    return $stacks;
  }

}
