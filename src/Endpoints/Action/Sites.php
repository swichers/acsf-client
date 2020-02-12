<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints\Action;

use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\Endpoints\ValidationTrait;
use swichers\Acsf\Client\Exceptions\MissingEntityException;

/**
 * ACSF Endpoint Wrapper: Sites.
 *
 * Sites are the product of the Site Factory. This resource is responsible for
 * managing those sites.
 *
 * @\swichers\Acsf\Client\Annotation\Action(
 *   name = "Sites",
 *   entityType = "Site"
 * )
 */
class Sites extends AbstractEntityAction {

  use ValidationTrait;

  /**
   * Gets a list of sites.
   *
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   A list of sites.
   *
   * @group Sites
   * @version v1
   * @title List sites
   * @http_method GET
   * @resource /api/v1/sites
   *
   * @params
   *   limit           | int  | no | A positive integer (max 100). | 10
   *   page            | int  | no | A positive integer.           | 1
   *   canary          | bool | no | No value necessary.           | false
   *   show_incomplete | bool | no | No value necessary.           | false
   *
   * @example_response
   * ```json
   *   {
   *     "count": 111,
   *     "sites": {
   *       {
   *         "id": 191,
   *         "site": "site1",
   *         "stack_id": 1,
   *         "domain": "domain1.site-factory.com"
   *         "groups": [
   *           91
   *         ],
   *         "site_collection": 176,
   *         "is_primary": true
   *       },
   *       {
   *         "id": 196,
   *         "site": "site2",
   *         "stack_id": 2,
   *         "domain": "domain2.site-factory.com",
   *         "groups": [
   *           91,
   *           105
   *         ],
   *         "site_collection": false,
   *         "is_primary": true
   *       }
   *     }
   *   }
   * ```
   */
  public function list(array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'limit',
        'page',
        'canary',
        'show_incomplete',
      ]
    );
    $options = $this->constrictPaging($options);
    if (isset($options['canary'])) {
      $options['canary'] = $this->ensureBool($options['canary']);
    }
    if (isset($options['show_incomplete'])) {
      $options['show_incomplete'] = $this->ensureBool(
        $options['show_incomplete']
      );
    }

    return $this->client->apiGet('sites', $options)->toArray();
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityType(): string {

    return 'Site';
  }

  /**
   * Create a new site.
   *
   * @param string $siteName
   *   The name of the new site.
   * @param array $options
   *   Additional request options.
   *
   * @return array
   *   The new site information.
   *
   * @version v1
   * @title Create a site
   * @http_method POST
   * @resource /api/v1/sites
   *
   * @params
   *   site_name       | string    | yes | The new site name.
   *   group_ids       | int|array | no  | Either a single group ID, or an
   *                                       array of group IDs.
   * install_profile   | string    | no  | The install profile to be used to
   *                                       install the site.
   * stack_id          | int       | ? | The stack id where the site should go.
   *
   * @group Sites
   *
   * @example_response
   * ```json
   *   {
   *     "id": 191,
   *     "site": "site1",
   *     "domains": [
   *       "mysite.site-factory.com"
   *     ]
   *   }
   * ```
   */
  public function create(string $siteName, array $options = []): array {

    $options = $this->limitOptions(
      $options,
      [
        'group_ids',
        'install_profile',
        'stack_id',
      ]
    );
    $options['site_name'] = $siteName;
    $options['stack_id'] = max(1, $options['stack_id'] ?? 1);

    if (isset($options['group_ids'])) {
      $options['group_ids'] = $this->cleanIntArray($options['group_ids']);
    }

    return $this->client->apiPost('sites', $options)->toArray();
  }

  /**
   * Initiates a backup for all sites.
   *
   * @param array $backupOptions
   *   An array of options to pass to the backup call.
   * @param bool $waitForComplete
   *   TRUE to wait for the task to complete before returning.
   * @param int $delaySeconds
   *   The number of seconds to wait before checks for task status.
   * @param callable|null $tickUpdate
   *   A function to call while waiting for the task to complete.
   *
   * @return array
   *   An array of task information.
   */
  public function backupAll(array $backupOptions = [], bool $waitForComplete = TRUE, int $delaySeconds = 30, callable $tickUpdate = NULL): array {
    $tasks = [];

    /** @var \swichers\Acsf\Client\Endpoints\Entity\Site $site */
    foreach ($this->getAll() as $site) {
      $backupOptions['label'] = sprintf('%s API-initiated backup', $site->details()['site']);
      $tasks[] = $site->backup($backupOptions);
    }

    if (!empty($tasks) && $waitForComplete) {
      foreach ($tasks as $task_info) {
        $this->client
          ->getEntity('Task', intval($task_info['task_id']))
          ->wait(max(1, $delaySeconds), $tickUpdate);
      }
    }

    return $tasks;
  }

  /**
   * Initiate a cache clear for all sites.
   *
   * @param bool $waitForComplete
   *   TRUE to wait for the task to complete before returning.
   * @param int $delaySeconds
   *   The number of seconds to wait before checks for task status.
   * @param callable|null $tickUpdate
   *   A function to call while waiting for the task to complete.
   *
   * @return array
   *   An array of task IDs.
   */
  public function clearCaches(bool $waitForComplete = TRUE, int $delaySeconds = 30, callable $tickUpdate = NULL): array {

    $task_ids = [];

    /** @var \swichers\Acsf\Client\Endpoints\Entity\Site $site */
    foreach ($this->getAll() as $site) {
      $cache_tasks = $site->clearCache();
      if (!empty($cache_tasks['task_ids'])) {
        $task_ids[] = intval($cache_tasks['task_ids']['drupal_cache_clear']);
        $task_ids[] = intval($cache_tasks['task_ids']['varnish_cache_clear']);
      }
    }

    $task_ids = array_unique($task_ids);
    $task_ids = array_filter($task_ids);

    if (!empty($task_ids) && $waitForComplete) {
      foreach ($task_ids as $task_id) {
        $this->client->getEntity('Task', $task_id)->wait(
          max(1, $delaySeconds),
          $tickUpdate
        );
      }
    }

    return $task_ids;
  }

  /**
   * Get a list of all available sites.
   *
   * @return array
   *   An array of all available sites.
   */
  public function listAll() : array {

    $sites = [
      'count' => 0,
      'sites' => [],
    ];

    $current_page = 1;

    do {

      $options = [
        'page' => $current_page,
        'limit' => 10,
      ];

      $site_info = $this->list($options);
      $sites['sites'] = array_merge($sites['sites'], $site_info['sites'] ?? []);

      $has_more =
        !empty($site_info['count']) &&
        ($site_info['count'] > ($current_page * $options['limit']));
      $current_page++;
    } while ($has_more);

    $sites['count'] = count($sites['sites']);

    return $sites;
  }

  /**
   * Get an array of all available sites as Site entities.
   *
   * @return array
   *   An array of Site entities.
   */
  public function getAll() : array {

    $sites = [];

    $sites_list = $this->listAll()['sites'] ?: [];

    $site_ids = array_column($sites_list, 'id');
    foreach ($site_ids as $id) {
      $sites[$id] = $this->get($id);
    }

    return $sites ?: [];
  }

  /**
   * Get a Site by its human name instead of ID.
   *
   * @param string $name
   *   The human name of a site factory site.
   *
   * @return \swichers\Acsf\Client\Endpoints\Entity\EntityInterface
   *   The site that matches the given name.
   *
   * @throws \swichers\Acsf\Client\Exceptions\MissingEntityException
   */
  public function getByName(string $name) : EntityInterface {

    $sites_list = $this->listAll()['sites'] ?? [];
    foreach ($sites_list as $info) {
      if ($info['site'] == $name) {
        return $this->get($info['id']);
      }
    }

    throw new MissingEntityException(sprintf('Unable to load Site with the name %s.', $name));
  }

}
