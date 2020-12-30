<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Entity;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * Group endpoint wrapper.
 *
 * @\swichers\Acsf\Client\Annotation\Entity(name = "Group")
 */
class Group extends AbstractEntity {

  use ValidationTrait;

  /**
   * Get the members of a group.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Users that are in the group.
   *
   * @version v1
   * @title List group members
   * @group Groups
   * @http_method GET
   * @resource /api/v1/groups/{group_id}/members
   *
   * @params
   *   limit     | int    | no | A positive integer (max 100). | 10
   *   page      | int    | no | A positive integer.           | 1
   *
   * @example_response
   * ```json
   *   {
   *     "time": "2016-11-25T13:18:44+00:00",
   *     "group_id": 123,
   *     "count": 3,
   *     "members": [
   *       {
   *         "uid": 101,
   *         "group owner": true,
   *         "group administrator": true
   *       },
   *       {
   *         "uid": 106,
   *         "group owner": false,
   *         "group administrator": true
   *       },
   *       {
   *         "uid": 111,
   *         "group owner": false,
   *         "group administrator": false
   *       }
   *     ]
   *   }
   * ```
   */
  public function members(array $options = []): array {

    $options = $this->limitOptions($options, ['limit', 'page']);
    $options = $this->constrictPaging($options);

    return $this->client->apiGet(['groups', $this->id(), 'members'], $options)
      ->toArray();
  }

  /**
   * Get a group by group ID.
   *
   * @return array
   *   Details about this group.
   *
   * @version v1
   * @title Get a group
   * @group Groups
   * @http_method GET
   * @resource /api/v1/groups/{group_id}
   *
   * @example_response
   * ```json
   *   {
   *     "created": 1399421609,
   *     "group_id": 123,
   *     "group_name": "mygroup",
   *     "owner": "user_name",
   *     "owner_id": 456,
   *     "parent_id": 789,
   *     "parent_name": "parentgroup",
   *     "live_site_count": 1,
   *     "total_site_count": 3,
   *     "status": 1
   *   }
   * ```
   */
  public function details(): array {

    return $this->client->apiGet(['groups', $this->id()])->toArray();
  }

}
