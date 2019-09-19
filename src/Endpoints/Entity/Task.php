<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;

/**
 * @Entity(name = "Task")
 */
class Task extends EntityBase {

  /**
   * Delete a task and its descendants.
   *
   * @version v1
   * @title Delete tasks from the work pool.
   * @group Tasks
   * @http_method DELETE
   * @resource /api/v1/tasks/{task_id}
   * @example_command
   *   curl '{base_url}/api/v1/tasks/123' \
   *     -v -u {user_name}:{api_key} -X DELETE \
   *     -H 'Content-Type: application/json'
   * @example_response
   *   {
   *     'message' : 'Task 123 has been deleted.',
   *     'time' : '1970-01-01T00:00:00+00:00',
   *     'task_id' : 123
   *   }
   */
  public function delete() : array {
    return $this->client->apiDelete(['tasks', $this->id()], [])->toArray();
  }

  /**
   * Terminate a task and its descendants.
   *
   * @version v1
   * @title Terminate tasks in the work pool.
   * @group Tasks
   * @http_method PUT
   * @resource /api/v1/tasks/{task_id}
   *
   * @example_command
   *   curl '{base_url}/api/v1/tasks/123' \
   *     -v -u {user_name}:{api_key} -X PUT \
   *     -H 'Content-Type: application/json'
   * @example_response
   *   {
   *     'message' : 'Task 123 has been queued for termination.',
   *     'time' : '1970-01-01T00:00:00+00:00',
   *     'task_id' : 123
   *   }
   */
  public function stop() : array {
    return $this->client->apiPut(['tasks', $this->id()], [])->toArray();
  }

  /**
   * Returns log entries about WIP tasks.
   *
   * @version v1
   * @title (Internal use only) Get Task log information.
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/tasks/{task_id}/logs
   *
   * @params
   *   level       | string | no | The minimum status level to display
   *   descendants | bool   | no | Whether to include the logs of all
   *   descendant tasks or not.
   *
   * @example_command
   *   curl '{base_url}/api/v1/tasks/123/logs?level=error' \
   *     -v -u {user_name}:{api_key} \
   *     -H 'Content-Type: application/json'
   * @example_response
   *   [
   *     ...
   *   ]
   */
  public function logs() : array {
    return $this->client->apiGet(['tasks', $this->id(), 'logs'])->toArray();
  }

  /**
   * Gets the status of a Wip task.
   *
   * @version v1
   * @title Wip task status
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/wip/task/%task_id/status
   *
   * @params
   *   task_id | int | yes | The Wip task ID
   *
   * @example_command
   *   curl '{base_url}/api/v1/wip/task/{%task_id}/status' \
   *     -H 'Content-Type: application/json' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "wip_task": {
   *        "id": "476",
   *        "parent": "0",
   *        "name": "SiteArchive 111",
   *        "group_name": "SiteArchive",
   *        "status": "16",
   *        "status_string": "Completed",
   *        "added": "1475051666",
   *        "started": "1475051667",
   *        "completed": "1475051765",
   *        "paused": "0",
   *        "error_message": "",
   *        "nid": "111",
   *        "uid": "21",
   *      }
   *     "time": "2014-05-02T16:21:25+00:00"
   *   }
   */
  public function status() : array {
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
   *   level | string  | no  | pauses/unpauses just the specified task or all
   *   its children. | task, family
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
