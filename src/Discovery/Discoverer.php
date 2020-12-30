<?php declare(strict_types = 1);

namespace swichers\Acsf\Client\Discovery;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Finder\Finder;

/**
 * Dynamic Endpoint and Action discoverer.
 */
class Discoverer implements DiscovererInterface {

  /**
   * The namespace to search for classes within.
   *
   * @var string
   */
  protected $namespace;

  /**
   * The directory to scan for class files.
   *
   * @var string
   */
  protected $directory;

  /**
   * The directory to base all paths from.
   *
   * @var string
   */
  protected $rootDir;

  /**
   * The fully-namespaced annotation class to scan for.
   *
   * @var string
   */
  protected $annotationClass;

  /**
   * A Doctrine annotation parser.
   *
   * @var \Doctrine\Common\Annotations\Reader
   */
  protected $annotationReader;

  /**
   * An array of items with annotations matching this Discoverer.
   *
   * @var array
   */
  protected $items = [];

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
  public function __construct(string $namespace, string $directory, string $rootDir, string $annotationClass, Reader $annotationReader) {

    $this->namespace = $namespace;
    $this->annotationReader = $annotationReader;
    $this->directory = $directory;
    $this->rootDir = $rootDir;
    $this->rootDir = __DIR__ . '/../..';
    $this->annotationClass = $annotationClass;
  }

  /**
   * {@inheritdoc}
   */
  public function getItems(): array {

    if (!$this->items) {
      $this->discoverItems();
    }

    return $this->items;
  }

  /**
   * Discovers items.
   */
  protected function discoverItems(): void {

    $path = $this->rootDir . '/src/' . $this->directory;
    $finder = new Finder();
    $finder->files()->in($path);

    /** @var \Symfony\Component\Finder\SplFileInfo $file */
    foreach ($finder as $file) {
      $class = $this->namespace . '\\' . $file->getBasename('.php');
      $annotation = $this->annotationReader->getClassAnnotation(
        new \ReflectionClass($class),
        $this->annotationClass
      );
      if (!$annotation) {
        continue;
      }

      /** @var \swichers\Acsf\Client\Annotation\Entity $annotation */
      $this->items[$annotation->getName()] = [
        'class' => $class,
        'annotation' => $annotation,
      ];
    }
  }

}
