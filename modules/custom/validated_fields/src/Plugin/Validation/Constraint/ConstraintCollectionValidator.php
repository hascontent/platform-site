<?php

namespace Drupal\validated_fields\Plugin\Validation\Constraint;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\validated_fields\Entity\ValidatedFieldInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


/**
 * Validates the ConstraintCollection constraint.
 */
class ConstraintCollectionValidator extends ConstraintValidator {

//validations

//length
  protected function length($entity, $constraint, array $params){
    $length = strlen($entity->getFieldValue());
    if(isSet($params['min']) && $length < $params['min']){
      $min = $params['min'];
      $this->context->addViolation($constraint->constraintViolation,
        ['%value'=> $entity->getFieldValue(), '%constraint' => 'length', '%message' => "must be at least $min characters long, was $length"]);
    }

    if(isSet($params['max']) && $length > $params['max']){
      $max = $params['max'];
      $this->context->addViolation($constraint->constraintViolation,
        ['%value'=> $entity->getFieldValue(), '%constraint' => 'length', '%message' => "must be at most $max characters long, was $length"]);
    }

  }
  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    foreach ($entity->getValidations() as $validation => $params) {
        $value = $entity->getFieldValue();
        $this->$validation($entity, $constraint, $params);

    }
  }
}
