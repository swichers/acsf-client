<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: VCS.
 *
 * Provides an API resource for viewing VCS information.
 *
 * The Site Factory platform deploys code to the hosting environment from git
 * repositories. The available refs in the git repository associated with a
 * particular site, whether that be the Factory or the sites, defines what can
 * be deployed to a given environment. The responsibility of this resource is to
 * list those refs.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Vcs")
 */
class Vcs extends AbstractAction {

  use ValidationTrait;

  /**
   * Get a list of VCS refs.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of VCS refs.
   *
   * @version v1
   * @title List deployable refs
   * @group VCS
   * @http_method GET
   * @resource /api/v1/vcs
   *
   * @params
   * type     | string | yes  | Either "sites" or "factory". (Note: "factory"
   *                              is restricted to Acquia employees.)
   * stack_id | string | ? | The stack id.
   *
   * @example_response
   * ```json
   *   {
   *     "available": [
   *       "dev-branch",
   *       "master",
   *       "tags\/2.84.0.3035",
   *       "tags\/2.85.0.3085",
   *       "tags\/some-other-tag"
   *     ],
   *     "current": "tags\/2.85.0.3085"
   *   }
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, ['stack_id']);
    $options['type'] = 'sites';
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    $this->requireOneOf($options['type'], ['sites', 'factory']);

    static $refs;
    $ref = &$refs[$options['stack_id']][$options['type']];
    if (is_null($ref)) {
      $ref = $this->client->apiGet('vcs', $options)->toArray();
    }

    return $ref;
  }

}
