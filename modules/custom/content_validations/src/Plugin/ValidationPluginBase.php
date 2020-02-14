<?php

namespace Drupal\content_validations\Plugin;

use Drupal\Component\Plugin\PluginBase;

use Drupal\Core\Field\FieldItemInterface;


/**
 * Base class for Validation plugin plugins.
 */
abstract class ValidationPluginBase extends PluginBase implements ValidationPluginInterface {


  // Add common methods and abstract methods for your plugin type here.


    /**
   * The function that validates the field based on parameters and values passed in
   * 
   * the field being validated
   * @param \Drupal\Core\Field\FieldItemInterface $field
   * 
   * @param array params the array of parameters for the validations
   */

  abstract public function validate(FieldItemInterface $field, array $params);
}
