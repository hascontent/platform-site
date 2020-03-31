<?php

namespace Drupal\validated_fields\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException; 

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "validated_fields_settings",
 *   label = @Translation("Validated fields settings"),
 *   uri_paths = {
 *     "canonical" = "/api/settings",
 *     "create" = "/api/settings"
 *   }
 * )
 */
class ValidatedFieldsSettings extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   *  Editable Settings Configuration Object
   * 
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->logger = $container->get('logger.factory')->get('validated_fields');
    $instance->currentUser = $container->get('current_user');
    $instance->config = \Drupal::configFactory()->getEditable("validated_fields.settings");
    $instance->currentRequest = $container->get('request_stack')->getCurrentRequest();
    return $instance;
  }

    /**
     * Responds to GET requests.
     *
     * @param string $payload
     *
     * @return \Drupal\rest\ResourceResponse
     *   The HTTP response object.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function get($payload) {

        // You must to implement the logic of your REST Resource here.
        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('administer content workflow entities')) {
            throw new AccessDeniedHttpException();
        }

        $res = new ResourceResponse(["interval_skip_weekends" => [
            "value" => $this->config->get("interval_skip_weekends"),
            "label" => "Task Intervals Should Skip Weekends",
            "description" => "If set to true, weekends will not be factored into due date calculations"
        ]]);

        $build = array(
            '#cache' => array(
              'max-age' => 0,
            ),
          );
        $res->addCacheableDependency($build);
        return $res;
    }

    /**
     * Responds to POST requests.
     *
     * @param string $payload
     *
     * @return \Drupal\rest\ModifiedResourceResponse
     *   The HTTP response object.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function post($payload) {
        // You must to implement the logic of your REST Resource here.
        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('administer content workflow entities')) {
            throw new AccessDeniedHttpException();
        }
        $params = json_decode($this->currentRequest->getContent(),TRUE);
        $this->config->set("interval_skip_weekends",$params["interval_skip_weekends"])->save();

        return new ModifiedResourceResponse("success", 200);
    }

}
