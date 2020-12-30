<?php

/**
 * @file
 * Initiate a generic code deployment.
 *
 * This script will deploy code to the target environment. It does not do a
 * backport or backup prior to code deployment.
 *
 * Usage:
 *   php redeploy.php dev
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

// The environment to redeploy code on.
define('TARGET_ENV', $argv[1] ?? 'dev');
// The ACSF stack to target.
define('STACK_ID', $argv[2] ?? 1);

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(TARGET_ENV);
run_script(
  'deploy',
  TARGET_ENV,
  $client->getAction('Vcs')->list()['current'],
  STACK_ID
);
$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
