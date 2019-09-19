<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;


use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Exceptions\InvalidEnvironmentException;

/**
 * Staging involves copying the Site Factory and a set of sites to a staging
 * environment.
 *
 * @Action(name = "Stage")
 */
class Stage extends ActionBase {

  /**
   * Starts the staging process.
   *
   * @version v1
   * @title Start staging process
   * @http_method POST
   * @resource /api/v1/stage
   * @group Tasks
   * @body
   *   to_env          | string  | yes | Environment to deploy to.
   *   sites           | array   | yes | Node IDs of sites to deploy.
   *   skip_gardener   | boolean | no  | Skip staging the Factory.
   *                  | false detailed_status | boolean | no  | Provide a
   *   status email for each site as it completes. | false
   * @example_command
   *   curl '{base_url}/api/v1/stage' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"to_env": "test", "sites": [96, 191]}'
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "message": "Staging deployment has been initiated - WIP123.",
   *     "task_id": 123
   *   }
   */
  public function stage($to_env, array $siteIds, array $options = []) : array {
    unset($options['to_env'], $options['sites']);

    $envs = $this->getEnvironments();
    if (!in_array($to_env, $envs)) {
      throw new InvalidEnvironmentException(
        'Provided environment was not listed as being a valid environment.'
      );
    }

    $siteIds = array_filter($siteIds);

    $options = [
      'to_env' => $to_env,
      'sites' => $siteIds,
      'wipe_target_environment' => $options['wipe_target_environment'] ?? FALSE,
      'synchronize_all_users' => $options['synchronize_all_users'] ?? TRUE,
      'detailed_status' => $options['detailed_status'] ?? FALSE,
    ];

    return $this->client->apiPost('stage', $options, 2)->toArray();
  }

  /**
   * Retrieves available environments user can stage to.
   *
   * @version v1
   * @title Retrieve available environments
   * @http_method GET
   * @resource /api/v1/stage
   * @group Tasks
   * @example_command
   *   curl '{base_url}/api/v1/stage' \
   *     -X GET -H 'Content-Type: application/json' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "test":"test"
   *   }
   */
  public function getEnvironments() : array {
    static $environments;
    if (is_null($environments)) {
      $result = $this->client->apiGet('stage', [], 2)->toArray();
      $environments = $result['environments'] ?? [];
    }

    return $environments;
  }

}
