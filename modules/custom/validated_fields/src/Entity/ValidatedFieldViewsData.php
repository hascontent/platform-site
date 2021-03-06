<?php

namespace Drupal\validated_fields\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Validated field entities.
 */
class ValidatedFieldViewsData extends EntityViewsData {

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
