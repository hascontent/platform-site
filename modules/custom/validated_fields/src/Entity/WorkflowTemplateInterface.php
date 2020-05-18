<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Workflow template entities.
 *
 * @ingroup validated_fields
 */
interface WorkflowTemplateInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Workflow template name.
   *
   * @return string
   *   Name of the Workflow template.
   */
  // public function getName();

  /**
   * Sets the Workflow template name.
   *
   * @param string $name
   *   The Workflow template name.
   *
   * @return \Drupal\validated_fields\Entity\WorkflowTemplateInterface
   *   The called Workflow template entity.
   */
  // public function setName($name);

  /**
   * Gets the Workflow template creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Workflow template.
   */
  public function getCreatedTime();

  /**
   * Sets the Workflow template creation timestamp.
   *
   * @param int $timestamp
   *   The Workflow template creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\WorkflowTemplateInterface
   *   The called Workflow template entity.
   */
  public function setCreatedTime($timestamp);

}
