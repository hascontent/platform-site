<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Stage action entities.
 *
 * @ingroup validated_fields
 */
interface StageActionInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Stage action name.
   *
   * @return string
   *   Name of the Stage action.
   */
  public function getName();

  /**
   * Sets the Stage action name.
   *
   * @param string $name
   *   The Stage action name.
   *
   * @return \Drupal\validated_fields\Entity\StageActionInterface
   *   The called Stage action entity.
   */
  public function setName($name);

  /**
   * Gets the Stage action creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Stage action.
   */
  public function getCreatedTime();

  /**
   * Sets the Stage action creation timestamp.
   *
   * @param int $timestamp
   *   The Stage action creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\StageActionInterface
   *   The called Stage action entity.
   */
  public function setCreatedTime($timestamp);

}
