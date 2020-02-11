<?php

/**
 * @file
 * Contains stage_action.page.inc.
 *
 * Page callback for Stage action entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Stage action templates.
 *
 * Default template: stage_action.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_stage_action(array &$variables) {
  // Fetch StageAction Entity Object.
  $stage_action = $variables['elements']['#stage_action'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
