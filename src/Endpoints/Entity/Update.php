<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

/**
 * Update endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Update")
 */
class Update extends AbstractEntity {

  /**
   * Resume update processing.
   *
   * @return array
   *   Update pause status.
   *
   * @see Update::pause()
   */
  public function resume(): array {

    return $this->pause(FALSE);
  }

  /**
   * Pause a running update process.
   *
   * @param bool $pause
   *   TRUE to pause the update. FALSE to resume.
   *
   * @return array
   *   Update status information.
   *
   * @version v1
   * @title Pause an update
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/update/pause
   * @body
   *   pause      | boolean | yes  | leave the task in paused or unpaused state.
   *
   * @example_response
   * ```json
   *   {
   *     "message": "Site update processing has been paused."
   *   }
   * ```
   */
  public function pause(bool $pause = TRUE): array {

    return $this->client->apiPost(
      [
        'update',
        $this->id(),
        'pause',
      ],
      ['pause' => $pause]
    )->toArray();
  }

  /**
   * Gets the status of a running update process.
   *
   * @return array
   *   The status of a running update process.
   *
   * @version v1
   * @title Get update progress
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/update/{update_id}/status
   *
   * @example_response
   * ```json
   *   {
   *     "statuses":{
   *       "not-started":0,
   *       "in-progress":0,
   *       "completed":"30",
   *       "warning":0,
   *       "error":0
   *     },
   *     "message":"Update complete",
   *     "percentage":100,
   *     "start_time": 1423862773,
   *     "end_time": 1423865337,
   *     "id":4726,
   *     "docroot_pairs":[
   *       {
   *         "environment":{
   *           "site":"test",
   *           "env":"prod",
   *           "tangle":"tangle_test",
   *           "type":"live_env"
   *         },
   *         "total":30,
   *         "error_percentage":0,
   *         "completed_percentage":100
   *       }
   *     ]
   *   }
   * ```
   */
  public function progress(): array {

    return $this->client->apiGet(['update', $this->id(), 'status'])->toArray();
  }

}
