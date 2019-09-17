<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Action;

use swichers\Acsf\Client\Exceptions\InvalidEnvironmentException;
use swichers\Acsf\Client\Exceptions\InvalidOptionException;
use function array_diff;
use function array_filter;
use function array_map;
use function asort;
use function is_numeric;

class Tasks extends BaseAction {

  public function stage($to_env, array $sites, array $options = []) {
    unset($options['to_env'], $options['sites']);

    $envs = $this->getEnvironments();
    if (!in_array($to_env, $envs)) {
      throw new InvalidEnvironmentException('Provided environment was not listed as being a valid environment.');
    }

    $sites = array_filter($sites);

    $options = [
        'to_env' => $to_env,
        'sites' => $sites,
        'wipe_target_environment' => FALSE,
        'synchronize_all_users' => TRUE,
        'detailed_status' => FALSE,
      ] + $options;

    return $this->client->apiPost('stage', $options, 2)->toArray();
  }

  public function getEnvironments() : array {
    $result = $this->client->apiGet('stage', [], 2)->toArray();
    return $result['environments'] ?? [];
  }

  public function updateCode(string $git_ref, array $options = []) {
    unset($options['sites_ref'], $options['factory_ref']);

    $options = [
        'scope' => 'sites',
        'start_time' => 'now',
        'sites_ref' => $git_ref,
        'factory_ref' => NULL,
        'sites_type' => 'code, db, registry',
        'factory_type' => 'code, db',
        'stack_id' => 1,
        'db_update_arguments' => '',
      ] + $options;

    $allowed_sites_type = ['code', 'db', 'registry'];
    $allowed_factory_type = ['code', 'db'];

    $norm = function ($types) {
      if (is_string($types)) {
        $types = explode(',', $types);
      }

      $types = array_map('trim', $types);
      $types = array_filter($types);
      $types = array_map('strtolower', $types);
      asort($types);

      return $types;
    };

    $options['sites_type'] = $norm($options['sites_type']);
    $options['factory_type'] = $norm($options['factory_type']);
    $options['scope'] = strtolower($options['scope']);
    $options['stack_id'] = min(1, $options['stack_id']);

    if (!in_array($options['scope'], ['both', 'sites', 'factory'])) {
      throw new InvalidOptionException();
    }
    elseif ($options['start_time'] !== 'now' || !is_numeric($options['start_time'])) {
      throw new InvalidOptionException();
    }
    elseif (!empty(array_diff($options['sites_type'], $allowed_sites_type))) {
      throw new InvalidOptionException();
    }
    elseif (!empty(array_diff($options['factory_type'], $allowed_factory_type))) {
      throw new InvalidOptionException();
    }

    return $this->client->apiPost('update', $options)->toArray();
  }

}
