<?php

namespace Drupal\validated_fields\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Action Record entities.
 *
 * @ingroup validated_fields
 */
interface ActionRecordInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Action Record creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Action Record.
   */
  public function getCreatedTime();

  /**
   * Sets the Action Record creation timestamp.
   *
   * @param int $timestamp
   *   The Action Record creation timestamp.
   *
   * @return \Drupal\validated_fields\Entity\ActionRecordInterface
   *   The called Action Record entity.
   */
  public function setCreatedTime($timestamp);

}
