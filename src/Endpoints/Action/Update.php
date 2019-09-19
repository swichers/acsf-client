<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Provides an API resource for updating the Site Factory platform.
 *
 * The Site Factory platform (the Factory and sites) need to be kept up-to-date.
 * The responsibility of this resource is to initiate updates of the Factory
 * and/or sites.
 *
 * A particular update may be concerned with a) updating the sites' code on the
 * filesystem, commonly referred to as a "hotfix", b) updating the code and
 * running database updates, or c) updating the code, running database updates,
 * and clearing the Drupal registry.
 *
 * Since the update process takes a long time, this method is non-blocking and
 * progress must be monitored either by watching the site update progress page
 * on the Factory or using the WIP status REST API endpoint.
 *
 * @Action(name = "Update")
 */
class Update extends ActionBase {

  /**
   * Start the update process.
   *
   * @version v1
   * @title Start an update
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/update
   * @body
   *   scope               | string | no  | Either "sites", "factory", or
   *   "both".                    | sites start_time          | mixed  | no  |
   *   A start time string, parseable by strtotime(), or "now". | now sites_ref
   *             | string | no  | A VCS ref to deploy to the sites. factory_ref
   *           | string | no  | A VCS ref to deploy to the Factory. sites_type
   *           | string | no  | Either "code", "code, db", or "code, db,
   *   registry"       | code, db factory_type        | string | no  | Either
   *   "code" or "code, db"                              | code, db stack_id
   *           | int    | no  | The stack id to release to. db_update_arguments
   *   | string | no  | Custom arguments to supply to the db-update hooks.
   *   Space separated alphanumeric characters only.
   * @example_command
   *   curl '{base_url}/api/v1/update' \
   *     -v -u {user_name}:{api_key} -X POST \
   *     -H 'Content-Type: application/json' \
   *     -d '{"scope": "sites", "sites_ref": "abcdef", "sites_type": "code"}'
   * @example_response
   *   {
   *     "message": "Update initiated.",
   *     "task_is": 123
   *   }
   */
  public function updateCode(string $git_ref, array $options = []) : array {
    $options = [
      'scope' => $options['scope'] ?? 'sites',
      'start_time' => $options['start_time'] ?? 'now',
      'sites_ref' => $git_ref,
      'factory_ref' => NULL,
      'sites_type' => $options['sites_type'] ?? 'code, db, registry',
      'factory_type' => $options['factory_type'] ?? 'code, db',
      'stack_id' => $options['stack_id'] ?? 1,
      'db_update_arguments' => $options['db_update_arguments'] ?? '',
    ];

    $allowed_sites_type = ['code', 'db', 'registry'];
    $allowed_factory_type = ['code', 'db'];

    $norm = function ($types) {
      if (is_string($types)) {
        $types = explode(',', $types);
      }

      $types = array_map('trim', $types);
      $types = array_filter($types);
      $types = array_map('strtolower', $types);
      asort($types);

      return $types;
    };

    $options['sites_type'] = $norm($options['sites_type']);
    $options['factory_type'] = $norm($options['factory_type']);
    $options['scope'] = strtolower($options['scope']);
    $options['stack_id'] = min(1, $options['stack_id']);

    if (!in_array($options['scope'], ['both', 'sites', 'factory'])) {
      throw new InvalidOptionException();
    }
    elseif ($options['start_time'] !== 'now' || !is_numeric(
        $options['start_time']
      )) {
      throw new InvalidOptionException();
    }
    elseif (!empty(array_diff($options['sites_type'], $allowed_sites_type))) {
      throw new InvalidOptionException();
    }
    elseif (!empty(
    array_diff(
      $options['factory_type'],
      $allowed_factory_type
    )
    )) {
      throw new InvalidOptionException();
    }

    return $this->client->apiPost('update', $options)->toArray();
  }

  /**
   * List update processes.
   *
   * @version v1
   * @title List updates
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/update
   * @example_command
   *   curl '{base_url}/api/v1/update' \
   *     -v -u {user_name}:{api_key} -X GET
   * @example_response
   *   {"1441":
   *     {"added":"1423762615","status":"16"},
   *    "1371":
   *     {"added":"1423760581","status":"16"},
   *    "1291":
   *     {"added":"1423741555","status":"16"}}
   *
   *
   */
  public function list(array $options = []) : array {
    return $this->client->apiGet('update')->toArray();
  }

  public function get(int $updateId) : EntityInterface {
    return $this->client->getEntity('Update', $updateId);
  }

}
