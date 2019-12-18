<?php

/**
 * @file
 * Contains validated_field.page.inc.
 *
 * Page callback for Validated field entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Validated field templates.
 *
 * Default template: validated_field.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_validated_field(array &$variables) {
  // Fetch ValidatedField Entity Object.
  $validated_field = $variables['elements']['#validated_field'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
