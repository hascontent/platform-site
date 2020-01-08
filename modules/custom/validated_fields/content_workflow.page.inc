<?php

/**
 * @file
 * Contains content_workflow.page.inc.
 *
 * Page callback for Content Workflow entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Content Workflow templates.
 *
 * Default template: content_workflow.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_content_workflow(array &$variables) {
  // Fetch ContentWorkflow Entity Object.
  $content_workflow = $variables['elements']['#content_workflow'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
