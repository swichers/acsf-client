<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Groups.
 *
 * Site Factories may use a feature called Site Groups in which sites can be
 * grouped together into meaningful sets. This resource provides methods for
 * managing such groups.
 *
 * @\swichers\Acsf\Client\Annotation\Action(
 *   name = "Groups",
 *   entityType = "Group"
 * )
 */
class Groups extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Get a list of groups.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of groups.
   *
   * @version v1
   * @title List groups
   * @group Groups
   * @http_method GET
   * @resource /api/v1/groups
   *
   * @params
   *   limit | int | no | A positive integer not higher than 100. | 10
   *   page  | int | no | A positive integer.                     | 1
   *
   * @example_response
   * ```json
   *   {
   *     "count": 123,
   *     "groups": [
   *       {
   *         "group_name": "test",
   *         "group_id": 10002246,
   *         "owner": "user_name",
   *         "owner_id": 10000461,
   *         "status": 1,
   *         "created": 1473142941,
   *         "live_site_count": 1,
   *         "total_site_count": 2
   *       },
   *       {
   *         "group_name": "subgroup",
   *         "group_id": 10002251,
   *         "owner": "user_name",
   *         "owner_id": 10000461,
   *         "parent_name": "test",
   *         "parent_id": 10002246,
   *         "status": 1,
   *         "created": 1473142941,
   *         "live_site_count": 0,
   *         "total_site_count": 1
   *       }
   *     ]
   *   }
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, ['limit', 'page']);
    $options = $this->constrictPaging($options);

    return $this->client->apiGet('groups', $options)->toArray();
  }

  /**
   * Create a site group.
   *
   * @param string $groupName
   *   The name of the site group to create.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The new group information.
   *
   * @version v1
   * @title Create a group
   * @group Groups
   * @http_method POST
   * @resource /api/v1/groups
   *
   * @params
   *   group_name | string | yes | The name of the site group to create.
   *   parent_id  | int    | no  | The parent group ID, if applicable.
   *
   * @example_response
   * ```json
   *   {
   *     "group_id": 123,
   *     "group_name": "mygroup"
   *   }
   * ```
   */
  public function create(string $groupName, array $options = []): array {

    $options = $this->limitOptions($options, ['parent_id']);
    $options['group_name'] = $groupName;

    return $this->client->apiPost('groups', $options)->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Group';
  }

}
