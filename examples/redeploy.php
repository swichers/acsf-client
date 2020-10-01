<?php
/**
 * @file
 * Redeploy the checked out tag or branch on the target environment.
 *
 * For example, can be used to easily redeploy develop-build on the dev
 * environment.
 *
 * Usage: php redeploy.php test
 * Usage: php redeploy.php dev 2
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

require __DIR__ . '/../vendor/autoload.php';

// The environment to redeploy code on.
define('TARGET_ENV', $argv[1] ?? '');
// The ACSF stack to target.
define('STACK_ID', $argv[2] ?? 1);

if (empty(TARGET_ENV)) {
  echo "Must supply a target environment.\n\n";
  printf(
    "Example: php %s test\n",
    basename(__FILE__)
  );
  die(1);
}

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(TARGET_ENV);

$refs = $client->getAction('Vcs')->list(['stack_id' => STACK_ID]);
printf("Redeploying code: %s\n", $refs['current']);

$task_info = $client->getAction('Update')->updateCode(
  $refs['current'],
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
