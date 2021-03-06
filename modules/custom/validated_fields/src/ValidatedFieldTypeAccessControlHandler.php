<?php

namespace Drupal\validated_fields;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Validated field type entity.
 *
 * @see \Drupal\validated_fields\Entity\ValidatedFieldType.
 */
class ValidatedFieldTypeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\validated_fields\Entity\ValidatedFieldTypeInterface $entity */

    switch ($operation) {

      case 'view':
      case 'update':
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'administer validated field type entities');
      default:
        return AccessResult::neutral();
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer validated field type entities');
  }


}
