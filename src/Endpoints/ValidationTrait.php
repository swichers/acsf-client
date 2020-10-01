<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Endpoints;

use swichers\Acsf\Client\Exceptions\InvalidOptionException;

/**
 * Shared Action and Entity validation helpers.
 */
trait ValidationTrait {

  /**
   * Set of common fields when dealing with backups.
   *
   * @var array
   */
  protected $backupFields = [
    'callback_url',
    'callback_method',
    'caller_data',
    'components',
  ];

  /**
   * Filters an options array down to a set of allowed items.
   *
   * @param array $options
   *   The options to filter.
   * @param array $allowedKeys
   *   The options that are allowed to remain.
   *
   * @return array
   *   An options array filtered down to allowed items.
   */
  protected function limitOptions(array $options, array $allowedKeys): array {

    return array_intersect_key($options, array_flip($allowedKeys));
  }

  /**
   * Ensures paging options have sane values.
   *
   * @param array $options
   *   The options to adjust.
   * @param int $maxLimit
   *   An alternative maximum number to use.
   *
   * @return array
   *   The options with paging values adjusted.
   */
  protected function constrictPaging(array $options, int $maxLimit = 100): array {

    if (isset($options['limit'])) {
      // Valid values are 1 to 100.
      $options['limit'] = max(1, min($options['limit'], $maxLimit));
    }

    if (isset($options['page'])) {
      $options['page'] = max(1, $options['page']);
    }

    if (isset($options['order'])) {
      $options['order'] = $this->ensureSortOrder($options['order']);
    }

    return $options;
  }

  /**
   * Ensures we have a valid sort order.
   *
   * @param string $order
   *   The sort order to check.
   *
   * @return string
   *   The sort order to use.
   */
  protected function ensureSortOrder(string $order) {

    return strtolower($order) == 'asc' ? 'asc' : 'desc';
  }

  /**
   * Ensures that the int array has consistent data types and values.
   *
   * @param array $values
   *   An array of values to process.
   *
   * @return array
   *   A clean and typed version of the array.
   */
  protected function cleanIntArray(array $values): array {

    $new_list = array_filter($values, 'is_numeric');
    $new_list = array_map('trim', $new_list);
    $new_list = array_map('intval', $new_list);
    $new_list = array_filter($new_list);
    $new_list = array_unique($new_list);
    $new_list = array_merge($new_list);

    return $new_list;
  }

  /**
   * Convert friendly bool text into actual bool values.
   *
   * Will change on, yes, true into a bool value. Leaves all other values up to
   * PHP's definition of truthiness.
   *
   * @param mixed $value
   *   The value to convert.
   *
   * @return bool
   *   TRUE if the string was truthy, FALSE otherwise.
   */
  protected function ensureBool($value): bool {

    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
  }

  /**
   * Validate that the backup options are valid.
   *
   * @param array $options
   *   Request options to check for backup keys.
   *
   * @return array
   *   Modified request options.
   */
  protected function validateBackupOptions(array $options) {

    if (isset($options['callback_url'])) {
      if (!filter_var($options['callback_url'], \FILTER_VALIDATE_URL)) {
        throw new InvalidOptionException(
          sprintf(
            'callback_url was set to an invalid url: %s',
            $options['callback_url']
          )
        );
      }
    }

    if (isset($options['callback_method'])) {
      $this->requireOneOf($options['callback_method'], ['GET', 'POST']);
    }

    if (isset($options['caller_data']) && is_array($options['caller_data'])) {
      $options['caller_data'] = json_encode($options['caller_data']);
    }

    if (isset($options['components'])) {
      if (!is_array($options['components'])) {
        throw new InvalidOptionException('Provided component(s) were invalid.');
      }

      $allowed_components = [
        'codebase',
        'database',
        'public files',
        'private files',
        'themes',
      ];

      $options['components'] = $this->filterArrayToValues(
        $options['components'],
        $allowed_components
      );

      if (empty($options['components'])) {
        throw new InvalidOptionException('Provided component(s) were invalid.');
      }
    }

    return $options;
  }

  /**
   * Ensure a string matches the given pattern.
   *
   * @param string $value
   *   The value to check.
   * @param string $regex
   *   The pattern to check against.
   *
   * @return true
   *   Returns TRUE when a pattern matches, and throws an exception otherwise.
   */
  protected function requirePatternMatch(string $value, string $regex): bool {

    if (!preg_match($regex, $value)) {
      throw new InvalidOptionException(
        sprintf('The value %s did not match the pattern %s.', $value, $regex)
      );
    }

    return TRUE;
  }

  /**
   * Reduce an array to the allowed values.
   *
   * @param array $original
   *   The original array.
   * @param array $allowedValues
   *   Values the array is allowed to have.
   * @param bool $toLowerCase
   *   TRUE to lowercase all array values.
   *
   * @return array
   *   The filtered array.
   */
  protected function filterArrayToValues(array $original, array $allowedValues, bool $toLowerCase = TRUE): array {

    $new = array_map('trim', $original);
    $new = array_filter($new);
    if ($toLowerCase) {
      $new = array_map('strtolower', $new);
      $allowedValues = array_map('strtolower', $allowedValues);
    }
    $new = array_intersect($new, $allowedValues);
    $new = array_unique($new);
    $new = array_merge($new);
    asort($new);

    return $new;
  }

  /**
   * Check if the given value is present in the allowed list.
   *
   * @param string $original
   *   The original value.
   * @param array $allowedValues
   *   An array of allowed values.
   * @param bool $toLowerCase
   *   TRUE to convert values to lowercase before comparing.
   *
   * @return bool
   *   TRUE if the value was found in the allowed list.
   */
  protected function requireOneOf(string $original, array $allowedValues, bool $toLowerCase = TRUE): bool {

    if ($toLowerCase) {
      $original = strtolower($original);
      $allowedValues = array_map('strtolower', $allowedValues);
    }
    $found = array_search($original, $allowedValues);
    if ($found === FALSE) {
      throw new InvalidOptionException(
        sprintf('Did not find %s in allowed values.', $original)
      );
    }

    return TRUE;
  }

}
