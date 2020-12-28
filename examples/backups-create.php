<?php

/**
 * @file
 * Backs up all sites on the target environment.
 *
 * Backs up db, theme, code, etc.
 *
 * Usage:
 *   php backup.php live 'database,public files'
 *   php backup.php live 'database,public files,private files'
 *   php backup.php live
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;
use swichers\Acsf\Client\Endpoints\Entity\EntityInterface;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

// The environment to back up (dev, live, etc.)
define('TARGET_ENV', $argv[1] ?? '');
// The comma separated list of components to backup.
define('BACKUP_COMPONENTS', $argv[2] ?? 'database,public files,private files');

if (empty(TARGET_ENV)) {
  echo "Must supply an environment to back up.\n\n";
  printf("Example: php %s live\n", basename(__FILE__));
  die(1);
}

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(TARGET_ENV);

printf("Creating backups including %s\n", BACKUP_COMPONENTS);

$client->getAction('Sites')->backupAll(
  [
    'components' => explode(',', BACKUP_COMPONENTS),
  ],
  TRUE,
  60,
  static function (EntityInterface $task, $taskStatus) use ($client) {

    $site_name = get_site_name($client, (int) $taskStatus['nid']);
    printf(
      "Backup (%d, %s): %s\n",
      $task->id(),
      $site_name,
      $taskStatus['status_string']
    );
  }
);

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);
