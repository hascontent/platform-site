<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Validated field entities.
 *
 * @ingroup validated_fields
 */
interface ValidatedFieldInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Validated field name.
   *
   * @return string
   *   Name of the Validated field.
   */
  public function getName();

  /**
   * Sets the Validated field name.
   *
   * @param string $name
   *   The Validated field name.
   *
   * @return \Drupal\validated_fields\Entity\ValidatedFieldInterface
   *   The called Validated field entity.
   */
  public function setName($name);

  /**
   * Gets the Validated field creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Validated field.
   */
  public function getCreatedTime();

  /**
   * Sets the Validated field creation timestamp.
   *
   * @param int $timestamp
   *   The Validated field creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\ValidatedFieldInterface
   *   The called Validated field entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Validated field - field store - property value
   * can return multiple types of values
   */
  public function getFieldValue();


}
