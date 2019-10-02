<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\ValidationTrait;

/**
 * ACSF Endpoint Wrapper: Update.
 *
 * Provides an API resource for updating the Site Factory platform.
 *
 * The Site Factory platform (the Factory and sites) need to be kept up-to-date.
 * The responsibility of this resource is to initiate updates of the Factory
 * and/or sites.
 *
 * A particular update may be concerned with a) updating the sites' code on the
 * filesystem, commonly referred to as a "hotfix", b) updating the code and
 * running database updates, or c) updating the code, running database updates,
 * and clearing the Drupal registry.
 *
 * Since the update process takes a long time, this method is non-blocking and
 * progress must be monitored either by watching the site update progress page
 * on the Factory or using the WIP status REST API endpoint.
 *
 * @\swichers\Acsf\Client\Annotation\Action(
 *   name = "Update",
 *   entityType="Update"
 * )
 */
class Update extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Start the update process.
   *
   * @param string $git_ref
   *   A VCS ref to deploy to the sites.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Update task information.
   *
   * @version v1
   * @title Start an update
   * @group Tasks
   * @http_method POST
   * @resource /api/v1/update
   * @body
   * scope        | string | no  | Either "sites", "factory", or "both". | sites
   * start_time   | mixed  | no  | A start time string, parsable by
   *                               strtotime(), or "now". | now
   * sites_ref    | string | no  | A VCS ref to deploy to the sites.
   * factory_ref  | string | no  | A VCS ref to deploy to the Factory.
   * sites_type   | string | no  | Either "code", "code, db", or "code, db,
   *                               registry"       | code, db
   * factory_type | string | no  | Either "code" or "code, db" | code, db
   * stack_id     | int    | no  | The stack id to release to.
   * db_update_arguments | string | no  | Custom arguments to supply to the
   *                                      db-update hooks. Space separated
   *                                      alphanumeric characters only.
   *
   * @example_response
   * ```json
   *   {
   *     "message": "Update initiated.",
   *     "task_is": 123
   *   }
   * ```
   */
  public function updateCode(string $git_ref, array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'scope',
        'start_time',
        'factory_ref',
        'sites_type',
        'factory_type',
        'stack_id',
        'db_update_arguments',
      ]
    );
    $options['sites_ref'] = $git_ref;
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    if (isset($options['scope'])) {
      $options['scope'] = strtolower($options['scope']);
      $this->requireOneOf($options['scope'], ['sites', 'factory', 'both']);
    }

    if (isset($options['db_update_arguments'])) {
      $this->requirePatternMatch(
        $options['db_update_arguments'],
        '/^[a-zA-Z0-9 ]+$/'
      );
    }

    $type_limits = [
      'sites_type' => ['code', 'db', 'registry'],
      'factory_type' => ['code', 'db'],
    ];
    foreach ($type_limits as $type => $limits) {
      if (isset($options[$type])) {
        $types =
          is_string($options[$type]) ? explode(',', $options[$type])
            : $options[$type];
        $options[$type] = implode(
          ', ',
          $this->filterArrayToValues($types, $limits)
        );
      }
    }

    return $this->client->apiPost('update', $options)->toArray();
  }

  /**
   * List update processes.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   Update process history.
   *
   * @version v1
   * @title List updates
   * @group Tasks
   * @http_method GET
   * @resource /api/v1/update
   *
   * @example_response
   * ```json
   *   {"1441":
   *     {"added":"1423762615","status":"16"},
   *    "1371":
   *     {"added":"1423760581","status":"16"},
   *    "1291":
   *     {"added":"1423741555","status":"16"}}
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions($options, []);

    return $this->client->apiGet('update', $options)->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Update';
  }

}
