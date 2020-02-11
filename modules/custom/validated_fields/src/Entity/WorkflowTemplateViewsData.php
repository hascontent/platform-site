<?php

namespace Drupal\validated_fields\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Workflow template entities.
 */
class WorkflowTemplateViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
