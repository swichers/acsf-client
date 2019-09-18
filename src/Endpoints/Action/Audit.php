<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * @Action(name = "Audit")
 */
class Audit extends ActionBase {

  /**
   * Gets a list of audit events.
   *
   * @version v1
   * @title List audit events
   * @group Audit
   * @http_method GET
   * @resource /api/v1/audit
   *
   * @params
   *   limit     | int    | no | A positive integer (max 100).     | 20
   *   page      | int    | no | A positive integer.               | 1
   *   order     | string | no | Either "ASC" or "DESC".           | DESC
   *   source    | string | no | The source of the event.          | null
   *   module    | string | no | The system name of the module.    | null
   *   scope     | string | no | The general scope of the changes. | null
   *   type      | string | no | The specific type of changes.     | null
   *   nid       | int    | no | An associated node ID.            | null
   *   uid       | int    | no | The user who made the change.     | null
   *
   * @example_command
   *   curl '{base_url}/api/v1/audit?nid=123' \
   *     -v -u {user_name}:{api_key} \
   * @example_response
   *   {
   *     "count": 1,
   *     "changes": [
   *       {
   *         "id": 1,
   *         "message": "Thing changed from @before to @after.",
   *         "before": "before_data",
   *         "after": "after_data",
   *         "type": "field_name",
   *         "scope": "context_name",
   *         "module": "system",
   *         "source": "Factory UI",
   *         "nid": 123,
   *         "uid": 456,
   *         "timestamp": 1403742194
   *       },
   *     ]
   *   }
   */
  public function list() : array {
    return $this->client->apiGet('audit')->toArray();
  }

}
