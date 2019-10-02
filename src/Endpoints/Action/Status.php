<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Status.
 *
 * Services such as site creation, site duplication, and domain management may
 * be disabled for short periods while their activity could be problematic. This
 * resource is responsible for managing the status of such services.
 *
 * @\swichers\Acsf\Client\Annotation\Action(name = "Status")
 */
class Status extends AbstractAction {

  use ValidationTrait;

  /**
   * Checks whether the API is responding.
   *
   * @return array
   *   A pong response.
   *
   * @version v1
   * @title Check service response
   * @group Status
   * @http_method GET
   * @resource /api/v1/ping
   *
   * @example_response
   * ```json
   *   {
   *     "message": "pong",
   *     "server_time": "2014-02-16T20:04:12-06:00"
   *   }
   * ```
   */
  public function ping(): array {

    return $this->client->apiGet('ping')->toArray();
  }

  /**
   * Get a status report.
   *
   * @return array
   *   The service status information.
   *
   * @version v1
   * @title Get status information
   * @group Status
   * @http_method GET
   * @resource /api/v1/status
   *
   * @example_response
   * ```json
   *   {
   *     "site_creation": "Disabled",
   *     "site_duplication": "Enabled",
   *     "domain_management": "Disabled until 2014-02-14T11:52:17-05:00",
   *     "bulk_operations": "Disabled until 2014-02-14T11:52:17-05:00"
   *   }
   * ```
   */
  public function get(): array {

    return $this->client->apiGet('status')->toArray();
  }

  /**
   * Modify the status.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Status information.
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
   *
   * @example_response
   * ```json
   *   {
   *     "site_creation": null,
   *     "site_duplication": null,
   *     "domain_management": null,
   *     "bulk_operations": null
   *   }
   * ```
   */
  public function set(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'all',
        'site_creation',
        'site_duplication',
        'domain_management',
        'bulk_operations',
      ]
    );

    // We want to convert on, off, yes, no, etc into a true bool, but don't want
    // to impact strtotime values in the process. We can make a best guess by
    // relying on strtotime to fail.
    foreach ($options as $key => $value) {
      if (!is_numeric($value) && FALSE === strtotime($value)) {
        $options[$key] = $this->ensureBool($value);
      }
    }

    return $this->client->apiPut('status', $options)->toArray();
  }

  /**
   * Gets the (release) version the Site Factory is on.
   *
   * @return array
   *   Site Factory version information.
   *
   * @version v1
   * @title Get the version of the Site Factory
   * @group Status
   * @http_method GET
   * @resource /api/v1/sf-info
   *
   * @example_response
   * ```json
   *   {
   *     "factory_version": "2.59.0",
   *     "time": "2017-05-11T18:15:19+00:00"
   *   }
   * ```
   */
  public function getSiteFactoryInfo(): array {

    return $this->client->apiGet('sf-info')->toArray();
  }

}
