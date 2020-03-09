<?php
namespace Drupal\triggered_events\Plugin\TriggeredEvents;

use Drupal\triggered_events\Plugin\TriggeredEventsBase;

use Drupal\Core\Field\FieldItemInterface;

/**
 * Validates that the length of text meets minimum and maximum requirements
 * 
 * @TriggeredEvents(
 *   params = {
 *     "to",
 *     "subject",
 *     "message"
 *   },
 *   id = "send_email",
 *   label = "Send Email",
 * )
 */
class SendEmail extends TriggeredEventsBase {
  /**
   * Executes the triggered event
    * 
   * @param array the array of parameters for the validations
   */
  public function execute2(array $params){
    $messages = [];
    $messages = simple_mail_send($params["from"],$params["to"],$params["subject"],$params["body"]);
    return $messages;
  }

  public function execute(array $params){
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'triggered_events';
    $key = 'my_mail'; // Replace with Your key
    $to = $params["to"] ?? \Drupal::currentUser()->getEmail() ?? "noemail";
    $params['message'] = $params["body"] ?? "";
    if(!array_key_exists("from", $params))
      $params["from"] = "noemail";
    $params['title'] = $params["subject"] ?? "";
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
  
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $params["from"], $send);
    if ($result['result'] != true) {
      $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
      drupal_set_message($message, 'error');
      \Drupal::logger('mail-log')->error($message);
      return;
    }
  
    $message = t('An email notification has been sent to @email from @from', array('@email' => $to, '@from' => $params["from"]));
    drupal_set_message($message);
    \Drupal::logger('mail-log')->notice($message);
  }
}