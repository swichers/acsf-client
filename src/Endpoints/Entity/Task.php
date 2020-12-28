<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\ValidationTrait;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Task endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Task")
 */
class Task extends AbstractEntity {

  use ValidationTrait;

  /**
   * Delete a task and its descendants.
   *
   * @return array
   *   Task information.
   *
   * @version v1
   * @title Delete tasks from the work pool.
   * @group Tasks
   * @http_method DELETE
   * @resource /api/v1/tasks/{task_id}
   *
   * @example_response
   * ```json
   *   {
   *     'message' : 'Task 123 has been deleted.',
   *     'time' : '1970-01-01T00:00:00+00:00',
   *     'task_id' : 123
   *   }
   * ```
   */
  public function delete(): array {

    return $this->client->apiDelete(['tasks', $this->id()], [])->toArray();
  }

  /**
   * Terminate a task and its descendants.
   *
   * @return array
   *   Stop task information.
   *
   * @version v1
   * @title Terminate tasks in the work pool.
   * @group Tasks
   * @http_method PUT
   * @resource /api/v1/tasks/{task_id}
   *
   * @example_response
   * ```json
   *   {
   *     'message' : 'Task 123 has been queued for termination.',
   *     'time' : '1970-01-01T00:00:00+00:00',
   *     'task_id' : 123
   *   }
   * ```
   */
  public function stop(): array {

    return $this->client->apiPut(['tasks', $this->id()], [])->toArray();
  }

  /**
   * Returns log entries about WIP tasks.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Task log entries.
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
   *                               descendant tasks or not.
   *
   * @example_response
   *   [
   *     ...
   *   ]
   */
  public function logs(array $options = []): array {

    $options = $this->limitOptions($options, ['level', 'descendants']);

    if (isset($options['level'])) {
      $allowed_values = [
        'emergency',
        'alert',
        'critical',
        'error',
        'warning',
        'notice',
        'info',
        'debug',
      ];
      $this->requireOneOf($options['level'], $allowed_values);
    }

    if (isset($options['descendants'])) {
      $options['descendants'] = $this->ensureBool($options['descendants']);
    }

    return $this->client->apiGet(['tasks', $this->id(), 'logs'], $options)
      ->toArray();
  }

  /**
   * Gets the status of a Wip task.
   *
   * @return array
   *   Status of the task.
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
   * @example_response
   * ```json
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
   * ```
   */
  public function status(): array {

    return $this->client->apiGet(['wip', 'task', $this->id(), 'status'])
      ->toArray();
  }

  /**
   * Pause or resume task processing.
   *
   * @param bool $paused
   *   TRUE to pause the task. FALSE to resume.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Task pause status.
   *
   * @version v1
   * @title Pause/resume task processing for a specific task
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/pause/%task_id
   * @body
   *   paused | boolean | yes | leave the task in paused or unpaused state.
   *   level | string  | no  | pauses/unpauses just the specified task or all
   *                            its children. | task, family
   *
   * @example_response
   * ```json
   *   {
   *     "message": "Task processing has been paused.",
   *     "task_id": 123,
   *     "level": "family"
   *   }
   * ```
   */
  public function pause(bool $paused = TRUE, array $options = []): array {

    $options = $this->limitOptions($options, ['level']);
    $options['paused'] = $paused;
    if (isset($options['level'])) {
      $this->requireOneOf($options['level'], ['family', 'task']);
    }

    return $this->client->apiPost(['pause', $this->id()], $options)->toArray();
  }

  /**
   * Resume task processing.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Task pause status.
   *
   * @see Task::pause()
   */
  public function resume(array $options = []): array {

    $options = $this->limitOptions($options, ['level']);

    return $this->pause(FALSE, $options);
  }

  /**
   * Wait for the task to finish.
   *
   * @param int $delaySeconds
   *   Number of seconds to wait between status checks.
   * @param callable|null $tickUpdate
   *   A callback function that executes each iteration.
   * @param string $statusKey
   *   The key to use for status checks. Will exit when finding a non-empty
   *   value.
   *
   * @return int
   *   The time spent waiting, in seconds.
   */
  public function wait(int $delaySeconds = 15, callable $tickUpdate = NULL, $statusKey = 'completed'): int {

    $statusKey = $statusKey ?: 'completed';
    $delaySeconds = $delaySeconds > 0 ? $delaySeconds : 1;

    $startTime = time();

    do {
      $task_status = $this->status()['wip_task'];
      if (!isset($task_status[$statusKey])) {
        throw new InvalidOptionException(
          sprintf('Status key %s was not found.', $statusKey)
        );
      }

      if (!empty($tickUpdate) && is_callable($tickUpdate)) {
        $tickUpdate($this, $task_status);
      }
    } while (empty($task_status[$statusKey]) && sleep($delaySeconds) !== FALSE);

    return time() - $startTime;
  }

}
