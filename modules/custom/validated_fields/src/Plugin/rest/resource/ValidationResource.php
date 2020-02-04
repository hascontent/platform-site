<?php

namespace Drupal\validated_fields\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Drupal\validated_fields\Plugin\Validation\Constraint\ConstraintCollectionValidator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "validation_resource",
 *   label = @Translation("Validation Resource"),
 *   uri_paths = {
 *     "canonical" = "/validations"
 *   }
 * )
 */
class ValidationResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The request object
   *
   * @var Request
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
     * @return ResourceResponse
     *   The HTTP response object.
     *
     * @throws HttpException
     *   Throws exception expected.
     */
    public function get($payload) {
        // You must to implement the logic of your REST Resource here.
        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        $qry = $this->currentRequest->query;
        $validations = ConstraintCollectionValidator::ALLOWED_FIELDS;
        return new ResourceResponse($validations, 200);
    }

}
