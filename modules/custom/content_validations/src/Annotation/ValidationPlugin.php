<?php

namespace Drupal\content_validations\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Validation plugin item annotation object.
 *
 * @see \Drupal\content_validations\Plugin\ValidationPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class ValidationPlugin extends Plugin {


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
   * The array of fields and fields properties, each element should have format:
   * "field_id1" => [
   *   "id" => "field_id1",
   *   "label" => "label for field_id1",
   *   "input" => "type of html input to use",
   *   "options" => "optional, use for selects and radios"
   * ] 
   *
   * @var array
   */
  public $input_fields;
  
  /**
   * Array of allowed fields
   * 
   * @var array
   */
  public $allowed_fields;
}
