<?php

namespace Drupal\validated_fields\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Checks that the submitted value is a unique integer.
 *
 * @Constraint(
 *   id = "ConstraintCollection",
 *   label = @Translation("Constraint Collection", context = "Validation"),
 *   type = "entity"
 * )
 */
class ConstraintCollection extends Constraint {


  // The message that will be shown if the value is not unique.
  public $constraintViolation = '%value failed constraint %constraint: %message';

}
