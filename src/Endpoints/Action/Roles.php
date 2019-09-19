<?php declare(strict_types = 1);


namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

/**
 * @Action(name = "Roles")
 */
class Roles extends ActionBase {

  /**
   * Gets a list of roles.
   *
   * @version v1
   * @title List roles
   * @group Role
   * @http_method GET
   * @resource /api/v1/roles
   *
   * @params
   *   limit       | int    | no | A positive integer (max 100).         | 10
   *   page        | int    | no | A positive integer.                   | 1
   *   order       | string | no | Either "ASC" or "DESC".               | ASC
   *
   * @example_command
   *   curl '{base_url}/api/v1/roles?order=DESC' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "count": 4,
   *     "roles": {
   *       "1": "anonymous user",
   *       "2": "authenticated user",
   *       "16": "release engineer",
   *       "21": "developer"
   *     }
   *   }
   */
  public function list(array $options = []) : array {
    return $this->client->apiGet('roles')->toArray();
  }


  public function get(int $roleId) : EntityInterface {
    return $this->client->getEntity('Role', $roleId);
  }

  /**
   * Create a role.
   *
   * @version v1
   * @title Create a role
   * @group Role
   * @http_method POST
   * @resource /api/v1/roles
   *
   * @params
   *   name | string | yes | The name of the role.
   *
   * @example_command
   *   curl '{base_url}/api/v1/roles' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"name": "content editor" }' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "role_id": 16,
   *     "role_name": "content editor"
   *   }
   */
  public function create(string $name) : array {
    return $this->client->apiPost('roles', ['name' => $name])->toArray();
  }

}
