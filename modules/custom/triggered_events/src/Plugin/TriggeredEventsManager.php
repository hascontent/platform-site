<?php

namespace Drupal\triggered_events\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Triggered Events plugin manager.
 */
class TriggeredEventsManager extends DefaultPluginManager {


  /**
   * Constructs a new TriggeredEventsManager object.
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
    parent::__construct('Plugin/TriggeredEvents', $namespaces, $module_handler, 'Drupal\triggered_events\Plugin\TriggeredEventsInterface', 'Drupal\triggered_events\Annotation\TriggeredEvents');

    $this->alterInfo('triggered_events_triggered_events_info');
    $this->setCacheBackend($cache_backend, 'triggered_events_triggered_events_plugins');
  }

}
