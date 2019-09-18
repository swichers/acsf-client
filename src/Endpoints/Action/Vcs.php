<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;


use swichers\Acsf\Client\Annotation\Action;

/**
 * Provides an API resource for viewing VCS information.
 *
 * The Site Factory platform deploys code to the hosting environment from git
 * repositories. The available refs in the git repository associated with a
 * particular site, whether that be the Factory or the sites, defines what can
 * be deployed to a given environment. The responsibility of this resource is to
 * list those refs.
 *
 * @package swichers\Acsf\Client\Endpoints\Action
 * @Action(name = "Vcs")
 */
class Vcs extends ActionBase {

  /**
   * Get a list of VCS refs.
   *
   * @version v1
   * @title List deployable refs
   * @group VCS
   * @http_method GET
   * @resource /api/v1/vcs
   *
   * @params
   *   type     | string | yes                      | Either "sites" or "factory". (Note: "factory" is restricted to Acquia employees.)
   *   stack_id | string | if multiple stacks exist | The stack id.
   *
   * @example_command
   *   curl '{base_url}/api/v1/vcs?type=sites' \
   *     -v -u {user_name}:{api_key}
   * @example_response
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
   *
   * @return array
   */
  public function list() : array {
    static $refs;
    if (is_null($refs)) {
      $options = [
        'type' => 'sites',
      ];
      $refs = $this->client->apiGet('vcs', $options)->toArray();
    }

    return $refs;
  }

}
