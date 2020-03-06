<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Stage entities.
 *
 * @ingroup validated_fields
 */
interface StageInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Stage name.
   *
   * @return string
   *   Name of the Stage.
   */
  public function getName();

  /**
   * Sets the Stage name.
   *
   * @param string $name
   *   The Stage name.
   *
   * @return \Drupal\validated_fields\Entity\StageInterface
   *   The called Stage entity.
   */
  public function setName($name);

  /**
   * Gets the Stage creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Stage.
   */
  public function getCreatedTime();

  /**
   * Sets the Stage creation timestamp.
   *
   * @param int $timestamp
   *   The Stage creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\StageInterface
   *   The called Stage entity.
   */
  public function setCreatedTime($timestamp);

}
