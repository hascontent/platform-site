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

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished validated field type entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published validated field type entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit validated field type entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete validated field type entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add validated field type entities');
  }


}
