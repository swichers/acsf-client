<?php declare(strict_types = 1);

namespace swichers\Acsf\Discovery;

use Doctrine\Common\Annotations\Reader;

interface DiscovererInterface {

  /**
   * Discoverer constructor.
   *
   * @param string $namespace
   *   The namespace to search for classes within.
   * @param string $directory
   *   The directory to scan for class files.
   * @param string $rootDir
   *   The directory to base all paths from.
   * @param string $annotationClass
   *   The fully-namespaced annotation class to scan for.
   * @param \Doctrine\Common\Annotations\Reader $annotationReader
   *   A Doctrine annotation parser.
   */
  public function __construct(string $namespace, string $directory, string $rootDir, string $annotationClass, Reader $annotationReader);

  /**
   * Scan for classes with annotations.
   *
   * @return array
   *   An array of discovered classes.
   */
  public function getItems() : array;

}
