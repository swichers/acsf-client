<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Tasks.
 *
 * @\swichers\Acsf\Client\Annotation\Action(
 *   name = "Tasks",
 *   entityType = "Task"
 * )
 */
class Tasks extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Pause or resume task processing.
   *
   * @param bool $paused
   *   Pauses/resumes the WIP task processing.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Response message.
   *
   * @version v1
   * @title Pause/resume task processing
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/pause
   * @body
   * paused | boolean | yes | Pauses/resumes the WIP task processing.
   * reason | string  | no  | Brief explanation for pausing workers.
   *
   * @example_response
   * ```json
   *   {
   *     "message": "Task processing has been paused."
   *   }
   * ```
   */
  public function pause(bool $paused, array $options = []): array {

    $options = $this->limitOptions($options, ['reason']);
    $options['paused'] = $paused;

    return $this->client->apiPost('pause', $options)->toArray();
  }

  /**
   * Returns data about WIP tasks.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Data about WIP tasks.
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
   *   class  | string | no | A WIP class name to filter on.
   *   group  | string | no | A WIP group name to filter on.
   *
   * @example_response
   * ```json
   *   [
   *     ...
   *   ]
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'limit',
        'page',
        'status',
        'group',
        'class',
      ]
    );

    if (isset($options['status'])) {
      $this->requireOneOf(
        $options['status'],
        ['processing', 'error', 'not-started']
      );
    }
    if (isset($options['class'])) {
      $this->requireOneOf(
        $options['class'],
        ['softpaused', 'softpause-for-update']
      );
    }

    $options = $this->constrictPaging($options);

    return $this->client->apiGet('tasks', $options)->toArray();
  }

  /**
   * Return data about WIP classes.
   *
   * @param string $type
   *   A WIP class name: softpaused or softpause-for-update.
   *
   * @return array
   *   Data about WIP classes
   *
   * @version v1
   * @title (Internal use only) Get Task class information.
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/classes/{type}
   *
   * @example_response
   * ```json
   *   [
   *     "Acquia\SfSite\SiteInstall",
   *     "Acquia\SfSite\SiteDuplicate"
   *   ]
   * ```
   */
  public function getClassInfo(string $type): array {

    $this->requireOneOf($type, ['softpaused', 'softpause-for-update']);

    return $this->client->apiGet(['classes', $type])->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Task';
  }

}
