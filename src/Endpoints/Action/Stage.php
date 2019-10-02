<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;
use swichers\Acsf\Client\Exceptions\InvalidEnvironmentException;

/**
 * ACSF Endpoint Wrapper: Stage.
 *
 * Staging involves copying the Site Factory and a set of sites to a staging
 * environment.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Stage")
 */
class Stage extends AbstractAction {

  use ValidationTrait;

  /**
   * Starts the staging process.
   *
   * @param string $to_env
   *   The target environment to backport to.
   * @param array $siteIds
   *   An array of site Ids to backport.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Information about the staging request.
   *
   * @version v2
   * @title Start staging process
   * @http_method POST
   * @resource /api/v2/stage
   * @group Tasks
   * @body
   *   to_env                  | string  | yes | Environment to deploy to.
   *   sites                   | array   | yes | Node IDs of sites to deploy.
   *   wipe_target_environment | boolean | no  | Use this option to wipe the
   *     management console and all stacks on the selected environment before
   *     deploying sites. | false
   *   synchronize_all_users   | boolean | no  | Use this parameter to only
   *    stage the user accounts that are required for the provided sites and
   *    the related site collections and site groups. | true
   *   detailed_status         | boolean | no  | Provide a status email for
   *    each site as it completes. | false
   *
   * @example_response
   * ```json
   *   {
   *     "message": "Staging deployment has been initiated - WIP123.",
   *     "task_id": 123
   *   }
   * ```
   */
  public function backport(string $to_env, array $siteIds, array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'synchronize_all_users',
        'detailed_status',
        'wipe_target_environment',
      ]
    );
    $options['sites'] = $this->cleanIntArray($siteIds);

    $bools = [
      'wipe_target_environment',
      'synchronize_all_users',
      'detailed_status',
    ];
    foreach ($bools as $key) {
      if (isset($options[$key])) {
        $options[$key] = $this->ensureBool($options[$key]);
      }
    }

    $envs = $this->getEnvironments();
    if (!in_array($to_env, $envs)) {
      throw new InvalidEnvironmentException(
        'Provided environment was not listed as being a valid environment.'
      );
    }
    $options['to_env'] = $to_env;

    return $this->client->apiPost('stage', $options, 2)->toArray();
  }

  /**
   * Retrieves available environments user can stage to.
   *
   * @version v2
   * @title Retrieve available environments
   * @http_method GET
   * @resource /api/v2/stage
   * @group Tasks
   *
   * @example_response
   * ```json
   *   {
   *     "environments": {
   *       "test" => "test"
   *     }
   *   }
   * ```
   */
  public function getEnvironments(): array {

    static $environments;
    if (is_null($environments)) {
      $result = $this->client->apiGet('stage', [], 2)->toArray();
      $environments = $result['environments'] ?? [];
    }

    return $environments;
  }

}
