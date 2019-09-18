<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;

/**
 * @Entity(name = "Task")
 */
class Task extends EntityBase {

  /**
   * Delete tasks from the work pool.
   * DELETE /api/v1/tasks/{task_id}
   */
  public function delete() : array {
    return $this->client->apiDelete(['tasks', $this->id()], [])->toArray();
  }

  /**
   * Terminate tasks in the work pool.
   * PUT /api/v1/tasks/{task_id}
   */
  public function stop() : array {
    return $this->client->apiPut(['tasks', $this->id()], [])->toArray();
  }

  /**
   * (Internal use only) Get Task log information.
   * GET /api/v1/tasks/{task_id}/logs
   */
  public function getLogInfo() : array {
    return $this->client->apiGet(['tasks', $this->id(), 'logs'])->toArray();
  }

  /**
   * Wip task status
   * GET /api/v1/wip/task/%task_id/status
   */
  public function getWipStatus() : array {
    return $this->client->apiGet(['wip', 'task', $this->id(), 'status'])
      ->toArray();
  }

  /**
   * Pause or resume task processing.
   *
   * @version v1
   * @title Pause/resume task processing for a specific task
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/pause/%task_id
   * @body
   *   paused | boolean | yes | leave the task in paused or unpaused state.
   *   level | string  | no  | pauses/unpauses just the specified task or all its children. | task, family
   * @example_command
   *   curl '{base_url}/api/v1/pause/123' \
   *     -v -u {user_name}:{api_key} -X POST \
   *     -H 'Content-Type: application/json' \
   *     -d '{"paused": true, "level": "family"}'
   * @example_response
   *   {
   *     "message": "Task processing has been paused.",
   *     "task_id": 123,
   *     "level": "family"
   *   }
   */
  public function pause() : array {
    return $this->client->apiPost(['pause', $this->id()], [])->toArray();
  }

}
