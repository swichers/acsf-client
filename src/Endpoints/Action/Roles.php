<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Roles.
 *
 * @\swichers\Acsf\Client\Annotation\Action(
 *   name = "Roles",
 *   entityType = "Role"
 * )
 */
class Roles extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Gets a list of roles.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of roles.
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
   * @example_response
   * ```json
   *   {
   *     "count": 4,
   *     "roles": {
   *       "1": "anonymous user",
   *       "2": "authenticated user",
   *       "16": "release engineer",
   *       "21": "developer"
   *     }
   *   }
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, ['limit', 'page', 'order']);
    $options = $this->constrictPaging($options);

    return $this->client->apiGet('roles', $options)->toArray();
  }

  /**
   * Create a role.
   *
   * @param string $name
   *   The name of the role to create.
   *
   * @return array
   *   The role information.
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
   * @example_response
   * ```json
   *   {
   *     "role_id": 16,
   *     "role_name": "content editor"
   *   }
   * ```
   */
  public function create(string $name): array {

    return $this->client->apiPost('roles', ['name' => $name])->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Role';
  }

}
