<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Validated field type entities.
 *
 * @ingroup validated_fields
 */
interface ValidatedFieldTypeInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Validated field type name.
   *
   * @return string
   *   Name of the Validated field type.
   */
  public function getName();

  /**
   * Sets the Validated field type name.
   *
   * @param string $name
   *   The Validated field type name.
   *
   * @return \Drupal\validated_fields\Entity\ValidatedFieldTypeInterface
   *   The called Validated field type entity.
   */
  public function setName($name);

  /**
   * Gets the Validated field type creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Validated field type.
   */
  public function getCreatedTime();

  /**
   * Sets the Validated field type creation timestamp.
   *
   * @param int $timestamp
   *   The Validated field type creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\ValidatedFieldTypeInterface
   *   The called Validated field type entity.
   */
  public function setCreatedTime($timestamp);

}
