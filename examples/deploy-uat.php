<?php
/**
 * @file
 * Deploy a new code reference to the UAT environment.
 *
 * Runs a backport of production to UAT first.
 *
 * Usage: php deploy-uat.php tags/2.7.0-beta.1-build
 * Usage: php deploy-uat.php master-build
 * Usage: php deploy-uat.php master-build 2
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientInterface;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\ServiceLoader;

require 'config.php';
require '../vendor/autoload.php';

// The code reference to deploy to uat.
define('DEPLOY_REF', $argv[1] ?? '');
// The ACSF stack to target.
define('STACK_ID', $argv[2] ?? 1);

// The environment to deploy to.
define('TARGET_ENV', 'uat');

if (empty(DEPLOY_REF)) {
  echo "Must supply a code reference.\n\n";
  printf(
    "Example: php %s tags/2.4.2-build\n",
    basename(__FILE__)
  );
  die(1);
}

$start_time = new DateTime();

$base_config = [
  'username' => API_USERNAME,
  'api_key' => API_KEY,
  'site_group' => ACSF_SITE_GROUP,
];

$client = ServiceLoader::buildFromConfig(
  ['acsf.client.connection' => ['environment' => 'live'] + $base_config]
)->get('acsf.client');

$task_id = start_production_backport($client, TARGET_ENV);
if (FALSE === $task_id) {
  die(1);
}

$client->getEntity('Task', $task_id)->wait(
  30,
  function (EntityInterface $task, array $task_status) {

    printf(
      "Backport (%d): %s\n",
      $task->id(),
      $task_status['status_string']
    );
  }
);

// Swap to the destination environment.
$client->setConfig(['environment' => TARGET_ENV] + $base_config);

$refs = $client->getAction('Vcs')->list(['stack_id' => STACK_ID]);
if (!in_array(DEPLOY_REF, $refs['available'])) {
  printf("Unable to find %s in list of available refs.\n", DEPLOY_REF);
  die(1);
}

printf("Current code: %s\n", $refs['current']);
printf("Deploying: %s\n", DEPLOY_REF);

$task_info = $client->getAction('Update')->updateCode(
  DEPLOY_REF,
  ['stack_id' => STACK_ID]
);
$client->getEntity('Task', intval($task_info['task_id']))->wait(
  30,
  function (EntityInterface $task, array $task_status) {

    printf(
      "Code Deploy (%d): %s\n",
      $task->id(),
      $task_status['status_string']
    );
  }
);

printf("Code deploy completed.\n");

print_site_code_status($client, intval($task_info['task_id']));

printf("Clearing site caches.\n");
$client->getAction('Sites')->clearCaches();
printf("Cache clear complete.\n");

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));
exit(0);

/**
 * Print the task status of the deployment task group.
 *
 * @param \swichers\Acsf\Client\ClientInterface $client
 *   The ACSF client.
 * @param int $parentId
 *   The parent group task ID.
 * @param int $depth
 *   The nesting level of the task in the group.
 */
function print_site_code_status(ClientInterface $client, int $parentId, int $depth = 0) {

  // Get the tasks associated with a task group.
  $tasks = array_filter(
    $client->getAction('Tasks')->list(),
    function ($task_info) use ($parentId, $depth) {

      return $task_info['parent'] == $parentId || ($task_info['id'] == $parentId && !$depth);
    }
  );

  // Re-order so they make sense.
  $tasks = array_reverse($tasks);

  foreach ($tasks as $task) {
    printf(
      "Task (%d:%d) %s: Finished at %s",
      $task['id'],
      $task['parent'],
      $task['name'],
      date('c', intval($task['completed']))
    );
    if (!empty($task['error_message'])) {
      printf(", Error: %s", $task['error_message']);
    }
    echo PHP_EOL;

    if ($task['id'] != $parentId) {
      print_site_code_status($client, intval($task['id']), $depth++);
    }
  }
}

/**
 * Starts a production backport.
 *
 * @param \swichers\Acsf\Client\ClientInterface $client
 *   An ACSF client.
 * @param string $toEnv
 *   The environment to backport to.
 *
 * @return int|false
 *   The backport task ID.
 */
function start_production_backport(ClientInterface $client, $toEnv = 'uat') {

  $original_config = $client->getConfig();
  $client->setConfig(['environment' => 'live'] + $original_config);

  $sites = $client->getAction('Sites')->listAll();
  $site_ids = array_column($sites['sites'] ?? [], 'id');
  if (empty($site_ids)) {
    printf("Unable to get site IDs to stage.\n");
    return FALSE;
  }

  printf(
    "Backporting %d sites from %s to %s: %s\n",
    count($site_ids),
    'live',
    'uat',
    implode(', ', $site_ids)
  );

  $task_info = $client->getAction('Stage')->backport(
    $toEnv,
    $site_ids,
    [
      'synchronize_all_users' => 'yes',
      'wipe_target_environment' => 'yes',
      'detailed_status' => 'no',
    ]
  );

  $task_id = $task_info['task_id'] ?? 0;
  if (empty($task_id)) {
    print_r($task_info);
    printf("Unable to get task ID.\n");
    return FALSE;
  }

  $client->setConfig($original_config);

  return intval($task_id);
}
