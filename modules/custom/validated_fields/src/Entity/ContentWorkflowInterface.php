<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Content Workflow entities.
 *
 * @ingroup validated_fields
 */
interface ContentWorkflowInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Content Workflow name.
   *
   * @return string
   *   Name of the Content Workflow.
   */
  public function getName();

  /**
   * Sets the Content Workflow name.
   *
   * @param string $name
   *   The Content Workflow name.
   *
   * @return \Drupal\validated_fields\Entity\ContentWorkflowInterface
   *   The called Content Workflow entity.
   */
  public function setName($name);

  /**
   * Gets the Content Workflow creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Content Workflow.
   */
  public function getCreatedTime();

  /**
   * Sets the Content Workflow creation timestamp.
   *
   * @param int $timestamp
   *   The Content Workflow creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\ContentWorkflowInterface
   *   The called Content Workflow entity.
   */
  public function setCreatedTime($timestamp);

}
