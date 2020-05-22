<?php

namespace Drupal\validated_fields\Plugin\rest\resource;

use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "workflow_activation",
 *   label = @Translation("Workflow activation"),
 *   uri_paths = {
 *     "canonical" = "/workflow/activate",
 *     "create" = "/workflow/activate"
 *   }
 * )
 */
class WorkflowActivation extends ResourceBase {

 /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    // $instance->logger = $container->get('logger.factory')->get('validated_fields');
    // $instance->currentUser = $container->get('current_user');
    // $instance->currentRequest = $container->get('request_stack')->getCurrentRequest();
    // return $instance;
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('example_rest'),
      $container->get('current_user'),
      $container->get('request_stack')->getCurrentRequest()
    );
  }

 /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param LoggerInterface $logger
   *   A logger instance.
   * @param AccountProxyInterface $current_user
   *   The current user instance.
   * @param Request $current_request
   *   The current request
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user, Request $current_request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->currentUser = $current_user;
    $this->currentRequest = $current_request;
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
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }

        return new ResourceResponse($payload, 200);
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
     * 
     * request params:
     *  workflow_id
     *  start_date
     */
    public function post($payload) {

        $params = json_decode($this->currentRequest->getContent(), TRUE);
        $workflow = \Drupal::EntityTypeManager()->getStorage('content_workflow')->load($params["workflow_id"]);
        $start_date = $params["start_date"];
        
        if($workflow->getAdminId() !== $this->currentUser->id()){
            throw new AccessDeniedHttpException;
        }
        $workflow->activateWorkflow($start_date);
        
        return new ModifiedResourceResponse($payload, 200);
    }

}
