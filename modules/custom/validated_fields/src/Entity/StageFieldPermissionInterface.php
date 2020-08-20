<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Stage field permission entities.
 *
 * @ingroup validated_fields
 */
interface StageFieldPermissionInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */


  /**
   * Gets the Stage field permission creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Stage field permission.
   */
  public function getCreatedTime();

  /**
   * Sets the Stage field permission creation timestamp.
   *
   * @param int $timestamp
   *   The Stage field permission creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\StageFieldPermissionInterface
   *   The called Stage field permission entity.
   */
  public function setCreatedTime($timestamp);

}
