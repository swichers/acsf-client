<?php

/**
 * @file
 * Backport production to the target environment.
 *
 * Usage: php backport.php test tags/2.7.0-beta.1-build
 * Usage: php backport.php test master-build
 * Usage: php backport.php dev develop-build 2
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

// The environment to backport to.
define('TARGET_ENV', $argv[1] ?? '');
// The code to deploy after the backport.
$DEPLOY_REF = $argv[2] ?? '';
// The ACSF stack to target.
define('STACK_ID', $argv[3] ?? 1);
// The environment to copy down to the TARGET_ENV.
define('SOURCE_ENV', 'live');

if (empty(TARGET_ENV)) {
  echo "Must supply a target environment and an optional code reference.\n\n";
  printf("Example: php %s test tags/2.4.2-build\n", basename(__FILE__));
  die(1);
}

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(SOURCE_ENV);

// If no code reference was supplied we default to the current code.
if (empty($DEPLOY_REF)) {
  $client->setEnvironment(TARGET_ENV);
  $DEPLOY_REF = $client->getAction('Vcs')->list()['current'];
  $client->setEnvironment(SOURCE_ENV);
}

$site_ids = array_column($client->getAction('Sites')->listAll()['sites'], 'id');
printf(
  "Backporting %d sites from %s to %s: %s\n",
  count($site_ids),
  SOURCE_ENV,
  TARGET_ENV,
  implode(', ', $site_ids)
);

$task_info = $client->getAction('Stage')->backport(
  TARGET_ENV,
  $site_ids,
  [
    'synchronize_all_users' => 'yes',
    'wipe_target_environment' => 'no',
    'detailed_status' => 'no',
  ]
);

$client->getEntity('Task', (int) $task_info['task_id'])->wait(
  60,
  static function (EntityInterface $task, array $taskStatus) {

    printf("Backport (%d): %s\n", $task->id(), $taskStatus['status_string']);
  }
);

run_script('deploy', TARGET_ENV, $DEPLOY_REF, STACK_ID);

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
