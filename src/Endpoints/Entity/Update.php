<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;

/**
 * @Entity(name = "Update")
 */
class Update extends EntityBase {

  /**
   * Pause a running update process.
   *
   * @version v1
   * @title Pause an update
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/update/pause
   * @body
   *   pause      | boolean | yes  | leave the task in paused or unpaused state.
   * @example_command
   *   curl '{base_url}/api/v1/update/123/pause' \
   *     -v -u {user_name}:{api_key} -X POST \
   *     -H 'Content-Type: application/json' \
   *     -d '{"pause": true"}'
   * @example_response
   *   {
   *     "message": "Site update processing has been paused."
   *   }
   */
  public function pause(bool $pause = TRUE) : array {
    return $this->client->apiPost(
      ['update', $this->id(), 'pause'],
      ['pause' => !!$pause]
    )->toArray();
  }

  public function resume() : array {
    return $this->pause(FALSE);
  }

  /**
   * Gets the status of a running update process.
   *
   * @version v1
   * @title Get update progress
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/update/{update_id}/status
   * @example_command
   *   curl '{base_url}/api/v1/update/123/status' \
   *     -v -u {user_name}:{api_key} \
   *     -H 'Content-Type: application/json'
   * @example_response
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
   */
  public function progress() : array {
    return $this->client->apiGet(['update', $this->id(), 'status'])->toArray();
  }

}
