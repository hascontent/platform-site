<?php

/**
 * @file
 * Contains workflow_template.page.inc.
 *
 * Page callback for Workflow template entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Workflow template templates.
 *
 * Default template: workflow_template.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_workflow_template(array &$variables) {
  // Fetch WorkflowTemplate Entity Object.
  $workflow_template = $variables['elements']['#workflow_template'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
