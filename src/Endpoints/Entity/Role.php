<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Annotation\Entity;

/**
 * @Entity(name = "Role")
 */
class Role extends EntityBase {

  /**
   * Retrieve a role by role ID.
   *
   * @version v1
   * @title Retrieve a role
   * @group Role
   * @http_method GET
   * @resource /api/v1/roles/{role_id}
   *
   * @params
   *   role_id | int | yes | The role ID of the user role.
   *
   * @example_command
   *   curl '{base_url}/api/v1/roles/{role_id}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "rid": 32,
   *     "name": "platform admin"
   *   }
   */
  public function details() {
    return $this->client->apiGet(['roles', $this->id()])->toArray();
  }

  /**
   * Update (rename) a role.
   *
   * @version v1
   * @title Update (rename) a role
   * @group Role
   * @http_method PUT
   * @resource /api/v1/roles/{role_id}/update
   *
   * @params
   *   new_name | string | yes | The new name for the user role.
   *
   * @example_command
   *   curl '{base_url}/api/v1/roles/32/update' \
   *     -X PUT -H 'Content-Type: application/json' \
   *     -d '{"new_name": "site builder"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "role_id": 32,
   *     "updated": true
   *   }
   */
  public function update(string $newName) : array {
    return $this->client->apiPut(
      ['roles', $this->id()],
      ['new_name' => $newName]
    )->toArray();
  }

  /**
   * Delete a role.
   *
   * @version v1
   * @title Delete a role
   * @group Role
   * @http_method DELETE
   * @resource /api/v1/roles/{role_id}
   * @example_command
   *   curl '{base_url}/api/v1/roles/76' \
   *     -X DELETE \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "role_id": 76,
   *     "deleted": true
   *   }
   */
  public function delete() : array {
    return $this->client->apiDelete(['roles', $this->id()], [])->toArray();
  }

}
