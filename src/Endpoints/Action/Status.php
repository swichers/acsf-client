<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Annotation\Action;

/**
 * @Action(name = "Status")
 */
class Status extends ActionBase {

  /**
   * Checks whether the API is responding.
   *
   * @version v1
   * @title Check service response
   * @group Status
   * @http_method GET
   * @resource /api/v1/ping
   * @example_command
   *   curl '{base_url}/api/v1/ping' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "message": "pong",
   *     "server_time": "2014-02-16T20:04:12-06:00"
   *   }
   */
  public function ping() : array {
    return $this->client->apiGet('ping')->toArray();
  }

  /**
   * Get a status report.
   *
   * @version v1
   * @title Get status information
   * @group Status
   * @http_method GET
   * @resource /api/v1/status
   * @example_command
   *   curl '{base_url}/api/v1/status' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "site_creation": "Disabled",
   *     "site_duplication": "Enabled",
   *     "domain_management": "Disabled until 2014-02-14T11:52:17-05:00",
   *     "bulk_operations": "Disabled until 2014-02-14T11:52:17-05:00"
   *   }
   */
  public function get() : array {
    return $this->client->apiGet('status')->toArray();
  }

  /**
   * Modify the status.
   *
   * @version v1
   * @title Modify status
   * @group Status
   * @http_method PUT
   * @resource /api/v1/status
   * @body
   *   all               | string | no | on, off, or something strtotime accepts
   *   site_creation     | string | no | on, off, or something strtotime accepts
   *   site_duplication  | string | no | on, off, or something strtotime accepts
   *   domain_management | string | no | on, off, or something strtotime accepts
   *   bulk_operations   | string | no | on, off, or something strtotime accepts
   * @example_command
   *   curl '{base_url}/api/v1/status' \
   *     -X PUT -d '{"all": "on"}' \
   *     -H 'Content-Type: application/json' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "site_creation": null,
   *     "site_duplication": null,
   *     "domain_management": null,
   *     "bulk_operations": null
   *   }
   */
  public function set(array $data) : array {
    return $this->client->apiPut('status', $data)->toArray();
  }

  /**
   * Gets the (release) version the Site Factory is on.
   *
   * @version v1
   * @title Get the version of the Site Factory
   * @group Status
   * @http_method GET
   * @resource /api/v1/sf-info
   * @example_command
   *   curl '{base_url}/api/v1/sf-info' \
   *     -v -u {user_name}:{api_key}
   * @example_response
   *   {
   *     "factory_version": "2.59.0",
   *     "time": "2017-05-11T18:15:19+00:00"
   *   }
   */
  public function getSiteFactoryInfo() : array {
    return $this->client->apiGet('sf-info')->toArray();
  }

}
