<?php

/**
 * @file
 * Initiate a generic code deployment.
 *
 * This script will deploy code to the target environment. It does not do a
 * backport or backup prior to code deployment.
 *
 * Usage:
 *   php deploy.php uat tags/2.17.0-build
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

// The environment to deploy code on.
define('TARGET_ENV', $argv[1] ?? '');
// The code reference to deploy.
define('CODE_REF', $argv[2] ?? '');
// The stack to target. Usually 1.
define('STACK_ID', $argv[3] ?? 1);

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(TARGET_ENV);

check_ref_exists($client, TARGET_ENV, CODE_REF);

$refs = $client->getAction('Vcs')->list();

printf("Deploying code for %s\n", TARGET_ENV);
printf("Current code: %s\n", $refs['current']);
printf("New code: %s\n", CODE_REF);

$task_info =
  $client->getAction('Update')->updateCode(CODE_REF, ['stack_id' => STACK_ID]);
$client->getEntity('Task', (int) $task_info['task_id'])->wait(
  60,
  static function (EntityInterface $task, array $task_status) {

    printf(
      "Code Deploy (%d): %s\n",
      $task->id(),
      $task_status['status_string']
    );
  }
);

printf("Deployment on %s is complete.\n", TARGET_ENV);

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
