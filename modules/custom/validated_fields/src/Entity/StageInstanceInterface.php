<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Stage Instance entities.
 *
 * @ingroup validated_fields
 */
interface StageInstanceInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Stage Instance name.
   *
   * @return string
   *   Name of the Stage Instance.
   */
  public function getName();

  /**
   * Sets the Stage Instance name.
   *
   * @param string $name
   *   The Stage Instance name.
   *
   * @return \Drupal\validated_fields\Entity\StageInstanceInterface
   *   The called Stage Instance entity.
   */
  public function setName($name);

  /**
   * Gets the Stage Instance creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Stage Instance.
   */
  public function getCreatedTime();

  /**
   * Sets the Stage Instance creation timestamp.
   *
   * @param int $timestamp
   *   The Stage Instance creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\StageInstanceInterface
   *   The called Stage Instance entity.
   */
  public function setCreatedTime($timestamp);

}
