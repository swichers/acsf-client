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

use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';

// The environment to backport to.
define('TARGET_ENV', $argv[1] ?? '');
// The code to deploy after the backport.
define('DEPLOY_REF', $argv[2] ?? '');
// The ACSF stack to target.
define('STACK_ID', $argv[3] ?? 1);
// The environment to copy down to the TARGET_ENV.
define('SOURCE_ENV', 'live');

if (empty(TARGET_ENV) || empty(DEPLOY_REF)) {
  echo "Must supply a target environment and a code reference.\n\n";
  printf(
    "Example: php %s test tags/2.4.2-build\n",
    basename(__FILE__)
  );
  die(1);
}

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(SOURCE_ENV);

$sites = $client->getAction('Sites')->listAll();
$site_ids = array_column($sites['sites'], 'id');

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

$client->getEntity('Task', intval($task_info['task_id']))->wait(
  30,
  function (EntityInterface $task, $task_status) {

    printf(
      "Backport (%d): %s\n",
      $task->id(),
      $task_status['status_string']
    );
  }
);

// Change to the target environment.
$client->setEnvironment(TARGET_ENV);

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
  function (EntityInterface $task, $task_status) {

    printf(
      "Code Deploy (%d): %s\n",
      $task->id(),
      $task_status['status_string']
    );
  }
);

printf("Code deploy completed.\n");

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
