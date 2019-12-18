<?php

/**
 * @file
 * Contains validated_field_type.page.inc.
 *
 * Page callback for Validated field type entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Validated field type templates.
 *
 * Default template: validated_field_type.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_validated_field_type(array &$variables) {
  // Fetch ValidatedFieldType Entity Object.
  $validated_field_type = $variables['elements']['#validated_field_type'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
