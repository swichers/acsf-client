<?php

/**
 * Assorted functions that are used in the different example scripts. This is
 * meant to be included directly.
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientInterface;

/**
 * Checks the given environment to see if the git reference exists.
 *
 * @param \swichers\Acsf\Client\ClientInterface $client
 *   The ACSF client.
 * @param string $targetEnv
 *   The environment to check for the git reference.
 * @param string $ref
 *   The ACSF git reference (tag or branch name, hash ID) to check for.
 */
function check_ref_exists(ClientInterface $client, string $targetEnv, string $ref) {

  $original_environment = $client->getEnvironment();
  $client->setEnvironment($targetEnv);

  $refs = $client->getAction('Vcs')->list();
  if (!in_array($ref, $refs['available'], TRUE)) {
    printf("Unable to find %s in list of available refs. Did you forget the -build suffix added by TravisCI?\n", $ref);
    die(1);
  }
  $client->setEnvironment($original_environment);
}

/**
 * Pause script execution.
 */
function pause() {

  printf("Paused. Press the return key to continue.\n");
  $fh = fopen('php://stdin', 'rb');
  fgets($fh);
  fclose($fh);
}

/**
 * Get the site name based on its ID.
 *
 * Used to avoid hitting the API multiple times in a loop.
 *
 * @param \swichers\Acsf\Client\ClientInterface $client
 *   The ACSF client.
 * @param int $siteId
 *   The Site ID.
 *
 * @return string
 *   The name of the site with the given ID.
 */
function get_site_name(ClientInterface $client, int $siteId): string {

  static $siteIds = [];

  if (!isset($siteIds[$siteId]) || is_null($siteIds[$siteId])) {
    /** @var \swichers\Acsf\Client\Endpoints\Entity\Site $site */
    $site = $client->getEntity('Site', $siteId);
    $siteIds[$siteId] = $site->details()['site'];
  }

  return $siteIds[$siteId];
}

/**
 * Run a script.
 *
 * @param string $scriptName
 *   The name of the script (without the php extension).
 * @param mixed ...$args
 *   Arguments to pass to the script.
 */
function run_script(string $scriptName, ...$args): void {

  array_unshift($args, sprintf('%s/%s.php', __DIR__, $scriptName));
  $args = array_map(
    static function ($element) {

      return is_int($element) ? $element : escapeshellarg($element);
    },
    $args
  );

  passthru('php ' . implode(' ', $args));
}
