<?php

/**
 * @file
 * Initiate a production deployment.
 *
 * Caution! This script will deploy code to production and uat environments.
 * It will create a backup on production first, but it will not create backups
 * on lower environments. UAT will be overwritten with production data before
 * its code is updated.
 *
 * Usage:
 *   php prod-release.php example tags/2.17.0-build tags/2.18.0-beta.1-build
 *   php prod-release.php example tags/2.17.0-build tags/2.18.0-beta.1-build test 2
 */

declare(strict_types = 1);

use swichers\Acsf\Client\ClientFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

for ($i = 1; $i <= 2; $i++) {
  if (empty($argv[$i])) {
    print "You must pass in an ACSF git reference for live and uat.\n";
    print "Usage: php prod-release.php example tags/2.17.0-build tags/2.18.0-beta.1-build\n";
    die(1);
  }
}

// The drush prefix to use when constructing the alias.
define('ALIAS_NAME', $argv[1] ?? '');
// The code reference to deploy to production.
define('LIVE_REF', $argv[2] ?? '');
// The code reference to deploy to the UAT environment.
define('UAT_REF', $argv[3] ?? '');
// Which environment to consider as 'UAT'.
define('UAT_ENV', $argv[4] ?? 'test');
// The stack to deploy to.
define('STACK_ID', $argv[5] ?? 1);

if (empty(ALIAS_NAME)) {
  echo "Must supply the alias prefix. If your alias is @example.01live then `example` is expected.\n\n";
  printf(
    "Example: php %s example tags/2.4.2-build tags/2.4.2-build\n",
    basename(__FILE__)
  );
  die(1);
}

if (empty(LIVE_REF) || empty(UAT_REF)) {
  echo "Must supply both a live and uat code reference.\n\n";
  printf(
    "Example: php %s example tags/2.4.2-build tags/2.4.2-build\n",
    basename(__FILE__)
  );
  die(1);
}

$start_time = new DateTime();

$client = ClientFactory::createFromEnvironment('live');

check_ref_exists($client, 'live', LIVE_REF);
check_ref_exists($client, UAT_ENV, UAT_REF);

pre_deploy_message();

pause();

// Grab all available site IDs.
// @TODO: This will not scale well.
$sites = $client->getAction('Sites')->listAll()['sites'] ?: [];
$site_ids = array_column($sites, 'id');
if (empty($site_ids)) {
  printf("Unable to get site IDs to stage.\n");
  die(1);
}

// Always back up production in case we have to revert changes.
run_script('backups-create', 'live');
run_script('deploy', 'live', LIVE_REF, STACK_ID);
blt_double_check(ALIAS_NAME, 'live', STACK_ID);
run_script('cc', 'live');
echo "Code finished deploying on production. Now is the time for any post-deployment steps.\n";
echo "If there were any deployment errors, you can try and solve them by running:\n";
display_error_helper_script();
echo "\n\nIt can be helpful to run this script anyway since the BLT deployment does not always fully import configuration.\n\n";
// Pause execution to allow for any post deployment steps to be run on live.
pause();

echo "Deploying code to UAT.\n";
run_script('backport', UAT_ENV, UAT_REF, STACK_ID);
blt_double_check(ALIAS_NAME, UAT_ENV, STACK_ID);
run_script('cc', UAT_ENV);

// Pause execution so any post-deployment steps can be run on lower sites.
pause();

print "Backporting, backups and deployments complete.\n";

$diff = $start_time->diff(new DateTime());
printf("Script complete. Time elapsed: %s\n", $diff->format('%H:%I:%S'));

post_deploy_message();

exit(0);

/**
 * Fake a BLT post-deployment script.
 *
 * Sometimes BLT does not properly execute the post-deploy config import and
 * updates. This will blindly execute an update and double config import. It's
 * harmless to run if there are no changes needed.
 *
 * @param string $prefix
 *   The drush alias prefix. @example.01live
 * @param string $env
 *   The environment to run commands against.
 * @param int $stackId
 *   The stack ID (if not 1).
 *
 * @return void
 */
function blt_double_check(string $prefix, string $env, int $stackId = 1): void {

  $alias = escapeshellarg(sprintf('@%s.%02d%s', $prefix, $stackId, $env));
  passthru(sprintf('drush %s sfml updatedb -y', $alias));
  passthru(sprintf('drush %s sfml cim sync -y', $alias));
  passthru(sprintf('drush %s sfml cim sync -y', $alias));
}

/**
 * Show the pre-deployment message.
 *
 * @return void
 */
function pre_deploy_message(): void {

  echo <<< 'EOM'

------------------------[ ACSF Production Release ]-------------------------

Deployment will take approximately 4-6 hours for the entire process to finish.
If there are post deployment steps, problems cutting releases, or problems
during the actual deployment this time may increase drastically.

Prior to starting the deployment, be sure to send out the deployment e-mail to
all interested parties. The following template may be used:

--------------------------------------------------------------------------------

We have a scheduled deployment for today. We will be deploying two sets of
changes to the different environments. Production will be receiving all items
that passed the UAT process. UAT will be receiving any newly QA'd items. Details
for both of these can be found on the respective JIRA tickets:

Production: 

UAT: 

The process is expected to take 4 hours, which includes time spent creating
backups, performing a dry-run on UAT, and smoke testing after the deployment.
UAT's deployment will follow production and may continue past the 4-hour window.
During this time production sites will remain available and accessible to the
public.

--------------------------------------------------------------------------------

EOM;
}

/**
 * Show the post-deployment message.
 *
 * @return void
 */
function post_deploy_message(): void {

  echo <<< 'EOM'
Deployment should be complete. If there were any errors during the process you
may need to manually resolve them. Often the following bash script will fix most
deployment issues:
EOM;

  display_error_helper_script();

  echo <<< 'EOM'

It can be helpful to run this script anyway since the BLT deployment does not always fully import configuration.

Review the above output for errors.

❗❗❗ Be sure to send the post-deployment completion e-mail. A reply-all to the pre-deployment e-mail will suffice. ❗❗❗

EOM;
}

/**
 * Show the import helper script.
 *
 * @return void
 */
function display_error_helper_script(): void {

  echo <<< 'EOM'

--------------------------------------------------------------------------------

drush @example.01env sfml updatedb -y
drush @example.01env sfml cim sync -y
drush @example.01env sfml cim sync -y

--------------------------------------------------------------------------------

❗❗❗ Adjust the environment as necessary to limit the affected sites. ❗❗❗

EOM;
}
