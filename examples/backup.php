<?php
/**
 * @file
 * Backs up all sites on the target environment.
 *
 * Backs up db, theme, code, etc.
 *
 * Usage: php backup.php live
 */

declare(strict_types = 1);

use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;
use swichers\Acsf\Client\ServiceLoader;

require 'config.php';
require '../vendor/autoload.php';

// The environment to back up.
define('TARGET_ENV', $argv[1] ?? '');

if (empty(TARGET_ENV)) {
  echo "Must supply an environment to back up.\n\n";
  printf(
    "Example: php %s live\n",
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
  ['acsf.client.connection' => ['environment' => TARGET_ENV] + $base_config]
)->get('acsf.client');

$client->getAction('Sites')->backupAll(
  ['components' => ['database']],
  TRUE,
  30,
  function (EntityInterface $task, $task_status) {

    printf(
      "Backup (%d): %s\n",
      $task->id(),
      $task_status['status_string']
    );
  }
);

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));
exit(0);
