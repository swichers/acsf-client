<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

/**
 * @Action(name = "Tasks")
 */
class Tasks extends ActionBase {


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
   * Returns data about WIP tasks.
   *
   * @version v1
   * @title (Internal use only) Get Task information.
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/tasks
   *
   * @params
   *   limit  | int    | no | A positive integer (max 100).    | 10
   *   page   | int    | no | A positive integer.              | 1
   *   status | string | no | processing, error or not-started
   *   status | class  | no | A WIP class name to filter on.
   *   group  | string | no | A WIP group name to filter on.
   *
   * @example_command
   *   curl '{base_url}/api/v1/tasks' \
   *     -v -u {user_name}:{api_key} \
   *     -H 'Content-Type: application/json'
   * @example_response
   *   [
   *     ...
   *   ]
   */
  public function list(array $options = []) : array {
    return $this->client->apiGet('tasks')->toArray();
  }

  /**
   * Return data about WIP classes
   *
   * @version v1
   * @title (Internal use only) Get Task class information.
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/classes/{type}
   * @example_command
   *   curl '{base_url}/api/v1/classes/softpaused' \
   *     -v -u {user_name}:{api_key} \
   *     -H 'Content-Type: application/json'
   * @example_response
   *   [
   *     "Acquia\SfSite\SiteInstall",
   *     "Acquia\SfSite\SiteDuplicate"
   *   ]
   */
  public function getClassInfo(string $type) : array {
    return $this->client->apiGet(['classes', $type])->toArray();
  }

  public function get(int $taskId) : EntityInterface {
    return $this->client->getEntity('Task', $taskId);
  }

}
