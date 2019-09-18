<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Exceptions\InvalidEnvironmentException;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;
use function array_diff;
use function array_filter;
use function array_map;
use function asort;
use function is_numeric;

/**
 * @Action(name = "Tasks")
 */
class Tasks extends ActionBase {

  /**
   * @param $to_env
   * @param array $siteIds
   * @param array $options
   *
   * @return mixed
   * @throws \swichers\Acsf\Client\Exceptions\InvalidEnvironmentException
   *
   * Start staging process
   * POST /api/v1/stage
   */
  public function stageEnvironment($to_env, array $siteIds, array $options = []) : array {
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
   * @return array
   * Retrieve available environments
   * GET /api/v1/stage
   */
  public function getEnvironments() : array {
    static $environments;
    if (is_null($environments)) {
      $result = $this->client->apiGet('stage', [], 2)->toArray();
      $environments = $result['environments'] ?? [];
    }

    return $environments;
  }

  /**
   * @param string $git_ref
   * @param array $options
   *
   * @return mixed
   * @throws \swichers\Acsf\Client\Exceptions\InvalidOptionException
   *
   * Start an update
   * POST /api/v1/update
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
   * Pause or resume task processing.
   *
   * @version v1
   * @title Pause/resume task processing
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/pause
   * @body
   * paused | boolean | yes | Pauses/resumes the WIP task processing.
   * reason | string  | no  | Brief explanation for pausing workers.
   * @example_command
   *   curl '{base_url}/api/v1/pause' \
   *     -v -u {user_name}:{api_key} -X POST \
   *     -H 'Content-Type: application/json' \
   *     -d '{"paused": true, "reason": "Reason for pausing workers"}'
   * @example_response
   *   {
   *     "message": "Task processing has been paused."
   *   }
   */
  public function pause() : array {
    return $this->client->apiPost('pause', [])->toArray();
  }

  /**
   * @param $updateId
   *
   * @return mixed
   *
   * Get update progress
   * GET /api/v1/update/{update_id}/status
   */
  public function getUpdateProgress($updateId) : array {
    return $this->client->apiGet(['update', $updateId, 'status'])->toArray();
  }

  /**
   * @return mixed
   *
   * List updates
   * GET /api/v1/update
   */
  public function listUpdates() : array {
    return $this->client->apiGet('update')->toArray();
  }

  /**
   * @return mixed
   * @throws \Exception
   *
   * Pause an update
   * POST /api/v1/update/pause
   */
  public function pauseUpdate() : array {
    return $this->client->apiPost(['update', 'pause'], [])->toArray();
  }

  /**
   * @internal
   * (Internal use only) Get Task information.
   * GET /api/v1/tasks
   */
  public function list() : array {
    return $this->client->apiGet('tasks')->toArray();
  }

  /**
   * (Internal use only) Get Task class information.
   * GET /api/v1/classes/{type}
   */
  public function getClassInfo(string $type) : array {
    return $this->client->apiGet(['classes', $type])->toArray();
  }

  public function get(int $taskId) : EntityInterface {
    return $this->client->getEntity('Task', $taskId);
  }

}
