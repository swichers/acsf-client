<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

/**
 * Role endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Role")
 */
class Role extends AbstractEntity {

  /**
   * Retrieve a role by role ID.
   *
   * @return array
   *   Information about this role.
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
   * @example_response
   * ```json
   *   {
   *     "rid": 32,
   *     "name": "platform admin"
   *   }
   * ```
   */
  public function details() {

    return $this->client->apiGet(['roles', $this->id()])->toArray();
  }

  /**
   * Update (rename) a role.
   *
   * @param string $newName
   *   The new name for the user role.
   *
   * @return array
   *   Role rename status.
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
   * @example_response
   * ```json
   *   {
   *     "role_id": 32,
   *     "updated": true
   *   }
   * ```
   */
  public function update(string $newName): array {

    return $this->client->apiPut(
      [
        'roles',
        $this->id(),
      ],
      ['new_name' => $newName]
    )->toArray();
  }

  /**
   * Delete a role.
   *
   * @return array
   *   Role delete status.
   *
   * @version v1
   * @title Delete a role
   * @group Role
   * @http_method DELETE
   * @resource /api/v1/roles/{role_id}
   *
   * @example_response
   * ```json
   *   {
   *     "role_id": 76,
   *     "deleted": true
   *   }
   * ```
   */
  public function delete(): array {

    return $this->client->apiDelete(['roles', $this->id()], [])->toArray();
  }

}
