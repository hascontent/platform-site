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
 *     "occurrences" = {
 *       "id" = "occurrences",
 *       "label" = "Occurrences",
 *       "input" = "text",
 *     }
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
    if($params["words"] === ""){
      return $messages;
    }
    $text = $field->value;
    foreach($params["words"] as $word){
      $occurrences = 1;
      if(isSet($word["occurrences"])){
        $occurrences = $word["occurrences"];
      }
      if(preg_match_all("/\b{$word["value"]}\b/i",$text) < $occurrences){
        $value = $word["value"];
          array_push($messages,"'$value' is required to appear $occurrences time(s)");
      }
    }
    return $messages;
  }
}
