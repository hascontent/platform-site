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
  //object that declares which validations can be associated with which fields
  const VALIDATIONS_LIST = array(
    "length" => [
      "text",
      "email",
      "string",
      "string_long",
      "text_long",
      "text_with_summary",
      "password"
    ],
    "blacklist" => [
      "text",
      "string",
      "string_long",
      "text_long",
      "text_with_summary"
    ],
    "requiredWords" => [
      "text",
      "string",
      "string_long",
      "text_long",
      "text_with_summary"
    ],
    "notNegative" => [
      "decimal",
      "float",
      "integer"
    ]
  );

  //object that associates fields with possible validation
  const ALLOWED_VALIDATIONS = array(
    "fields" => array(
      "text" => array(
        "requiredWords",
        "blacklist",
        "length"
      ),
      "string" => array(
        "requiredWords",
        "blacklist",
        "length"
      ),
      "string_long" => array(
        "requiredWords",
        "blacklist",
        "length"
      ),
      "text_long" => array(
        "requiredWords",
        "blacklist",
        "length"
      ),
      "text_with_summary" => array(
        "requiredWords",
        "blacklist",
        "length"
      ),
      "password" => array(
        "length"
      ),
      "decimal" => array(
        "notNegative"
      ),
      "float" => array(
        "notNegative"
      ),
      "integer" => array(
        "notNegative"
      ),
    ),
    "validations" => array(
      "notNegative" => [
        "label" => "Not Negative"
      ],
      "requiredWords" => [
        "label" => "Required Words",
        "fields" => [
          "Word List"
        ]
        ],
      "blacklist" => [
        "label" => "Prohibited Words",
        "fields" => [
          "Word List"
        ]
      ],
      "length" => [
        "label" => "Character Count",
        "fields" => [
          "Minimum",
          "Maximum"
        ]
      ],
    )
  );
//validations

//length
  // protected function length($entity, $constraint, array $params){
  //   $length = strlen($entity->getFieldValue());
  //   if(isSet($params['min']) && $length < $params['min']){
  //     $min = $params['min'];
  //     $this->context->addViolation($constraint->constraintViolation,
  //       ['%value'=> $entity->getFieldValue(), '%constraint' => 'length', '%message' => "must be at least $min characters long, was $length"]);
  //   }

  //   if(isSet($params['max']) && $length > $params['max']){
  //     $max = $params['max'];
  //     $this->context->addViolation($constraint->constraintViolation,
  //       ['%value'=> $entity->getFieldValue(), '%constraint' => 'length', '%message' => "must be at most $max characters long, was $length"]);
  //   }

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
   $type = $entity->getStorageTypeId();

   if(!isSet($type)){
     $this->context->addViolation($constraint->noType);
     return;
   }
   foreach($entity->getValidations() as $validation => $params){
     if(!isSet(self::VALIDATIONS_LIST[$validation])){
       $this->context->addViolation($constraint->nonexistentValidation, ['%validation' => $validation]);
       continue;
     }
       if(!in_array($type,self::VALIDATIONS_LIST[$validation])){
       $this->context->addViolation($constraint->incorrectValidation, ['%validation' => $validation, '%field' => $type]);
     }
   }

//   these validations are deprecated
//   they are used to validate fields rather than the correctness
//   of the validators the user has selected for their field
//    foreach ($entity->getValidations() as $validation => $params) {
//        $value = $entity->getFieldValue();
//        $this->$validation($entity, $constraint, $params);
//
//    }
  }
}
