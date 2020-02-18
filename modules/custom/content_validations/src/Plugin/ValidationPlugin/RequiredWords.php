<?php
namespace Drupal\content_validations\Plugin\ValidationPlugin;

use Drupal\content_validations\Plugin\ValidationPluginBase;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Validates that the length of text meets minimum and maximum requirements
 * 
 * @ValidationPlugin(
 *   input_fields = {
 *     "words" = {
 *       "id" = "words",
 *       "label" = "Words",
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
 *   id = "required_words",
 *   label = "Required Words",
 * )
 */

class RequiredWords extends ValidationPluginBase {
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
    $words = explode( ", ", $params["words"]);
    foreach($words as $word){
      if(!preg_match("/\b{$word}\b/i",$text)){
          array_push($messages,"Word is required: '$word'");
      }
    }
    return $messages;
  }
}