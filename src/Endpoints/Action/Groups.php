<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Endpoints\PagingTrait;

/**
 * Site Factories may use a feature called Site Groups in which sites can be
 * grouped together into meaningful sets. This resource provides methods for
 * managing such groups.
 *
 * @Action(name = "Groups")
 */
class Groups extends Actionbase {

  use PagingTrait;

  /**
   * Get a list of groups.
   *
   * @throws Exception
   *
   * @example_response
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
   * @example_command
   *   curl '{base_url}/api/v1/groups?page=2&limit=20' \
   *     -v -u {user_name}:{api_key}
   *
   */
  public function list(array $options = []) : array {
    $options = $this->validatePaging($options);
    return $this->client->apiGet('groups', $options)->toArray();
  }

  /**
   * Get a group by group ID.
   */
  public function get(int $groupId) : EntityInterface {
    return $this->client->getEntity('Group', $groupId);
  }

  /**
   * Create a site group.
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
   * @example_command
   *   curl '{base_url}/api/v1/groups' \
   *     -X POST -H 'Content-Type: application/json' \
   *     -d '{"group_name": "mygroup"}' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "group_id": 123,
   *     "group_name": "mygroup"
   *   }
   */
  public function create(string $groupName, array $options = []) : array {

  }

}
