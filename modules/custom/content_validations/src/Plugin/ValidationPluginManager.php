<?php

namespace Drupal\content_validations\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Validation plugin plugin manager.
 */
class ValidationPluginManager extends DefaultPluginManager {


  /**
   * Constructs a new ValidationPluginManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ValidationPlugin', $namespaces, $module_handler, 'Drupal\content_validations\Plugin\ValidationPluginInterface', 'Drupal\content_validations\Annotation\ValidationPlugin');

    $this->alterInfo('content_validations_validation_plugin_info');
    $this->setCacheBackend($cache_backend, 'content_validations_validation_plugin_plugins');
  }

}
