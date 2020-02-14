<?php
namespace Drupal\content_validations\Plugin\ValidationPlugin;

use Drupal\content_validations\Plugin\ValidationPluginBase;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Validates that the length of text meets minimum and maximum requirements
 * 
 * @ValidationPlugin(
 *   input_fields = {
 *     "min" = {
 *       "id" = "min",
 *       "label" = "Minimum",
 *       "input" = "text",
 *     },
 *     "max" = {
 *       "id" = "max",
 *       "label" = "Maximum",
 *       "input" = "text",
 *     },
 *   },
 *   allowed_fields = {
 *     "text",
 *     "email",
 *     "string",
 *     "string_long",
 *     "text_long",
 *     "text_with_summary",
 *     "password",
 *   },
 *   id = "length",
 *   label = "Length",
 * )
 */

class LengthValidation extends ValidationPluginBase {
    /**
     * The function that validates the field based on parameters and values passed in
     * 
     * the field being validated
     * @param \Drupal\Core\Field\FieldItemInterface $field
     * 
     * @param array params the array of parameters for the validations
     */
    public function validate(FieldItemInterface $field, array $params){
        $messages = [];
        $text = $field->value;
        $length = strlen($text);
        $max = $params["max"];
        $min = $params["min"];
        if($text > $params["max"]){
            array_push($messages, "Field must be at most $max characters long");
        }
        if($text < $params["min"]){
            array_push($messages, "Field must be at least $min characters long");
        }
        return $messages;
    }
 }