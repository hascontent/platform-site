<?php

namespace Drupal\triggered_events\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Triggered Events item annotation object.
 *
 * @see \Drupal\triggered_events\Plugin\TriggeredEventsManager
 * @see plugin_api
 *
 * @Annotation
 */
class TriggeredEvents extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The required parameters for the execute function
   * 
   * @var array
   */
  public $params;

}
