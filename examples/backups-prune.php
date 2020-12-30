<?php

/**
 * @file
 * Deletes backups older than the given date.
 *
 * Usage:
 *   php backups-prune.php dev 14
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;
use swichers\Acsf\Client\ClientInterface;
use swichers\Acsf\Client\Endpoints\Entity\Site;
use Symfony\Component\HttpClient\Exception\ClientException;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

// The environment to remove backups from.
define('TARGET_ENV', $argv[1] ?? '');
// The age of backups to remove. Backups older than this will be deleted.
define('MAX_AGE_DAYS', (int) ($argv[2] ?? 14));

if (empty(TARGET_ENV)) {
  echo "Must supply an environment to back up.\n\n";
  printf("Example: php %s live\n", basename(__FILE__));
  die(1);
}

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment(TARGET_ENV);

printf("Removing site backups older than %d days.\n", MAX_AGE_DAYS);

/** @var \swichers\Acsf\Client\Endpoints\Entity\Site $site */
foreach ($client->getAction('Sites')->getAll() as $site) {
  prune_site_backups($client, $site, MAX_AGE_DAYS);
}

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

exit(0);

/**
 * Prune old backups for the given site.
 *
 * @param \swichers\Acsf\Client\ClientInterface $client
 *   The ACSF client.
 * @param \swichers\Acsf\Client\Endpoints\Entity\Site $site
 *   The Site to prune backups for.
 * @param int $maxAge
 *   The age of backups to remove. Defaults to 2 weeks.
 * @param int|null $currentPage
 *   The current page.
 * @param int $perPage
 *   The amount of backups to list per page.
 *
 * @throws \swichers\Acsf\Client\Exceptions\InvalidOptionException
 */
function prune_site_backups(ClientInterface $client, Site $site, int $maxAge = 14, int $currentPage = NULL, int $perPage = 20) {

  if (is_null($currentPage)) {
    // We need to calculate what the last page is so we can work our way
    // backwards. If we start at the front we may miss items as page numbers
    // shift during the loop.
    $currentPage =
      (int) ceil($site->listBackups(['limit' => 1])['count'] / $perPage) ?: 1;
  }

  try {
    $backups = $site->listBackups(
      [
        'limit' => $perPage,
        'page' => $currentPage,
      ]
    );
  }
  catch (ClientException $x) {
    fwrite(STDERR, $x->getMessage() . PHP_EOL);
    return;
  }

  $prunables = array_filter(
    $backups['backups'],
    static function ($backup) use ($maxAge) {

      return $backup['timestamp'] <= strtotime(sprintf('-%d days', $maxAge));
    }
  );

  if (!empty($prunables)) {
    foreach ($prunables as $prunable) {
      printf(
        "%s: Delete %s %s\n",
        $site->details()['site'],
        $prunable['label'],
        date('c', $prunable['timestamp'])
      );
      /** @var \swichers\Acsf\Client\Endpoints\Entity\Backup $backup */
      $backup = $client->getEntity('Backup', $prunable['id'], $site);
      $backup->delete();
    }
  }

  if ($currentPage > 1) {
    prune_site_backups($client, $site, $maxAge, $currentPage - 1, $perPage);
  }
}
