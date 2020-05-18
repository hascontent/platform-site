<?php

namespace Drupal\validated_fields\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\ModifiedResourceResponse;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Drupal\validated_fields\Entity\StageInstance;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "stage_transition",
 *   label = @Translation("Stage Transition"),
 *   uri_paths = {
 *     "canonical" = "/api/stage/{id}",
 *     "create" = "/api/stages"
 *   },
 *   serialization_class = ""
 * )
 */
class StageTransition extends ResourceBase {

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
    public function get($id) {

        // You must to implement the logic of your REST Resource here.
        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        $res = ["id" => $id];
        return new ResourceResponse($res, 200);
    }

    /**
     * Responds to PUT requests.
     *
     * @param string $payload
     *
     * @return \Drupal\rest\ModifiedResourceResponse
     *   The HTTP response object.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function put($id, array $data = []) {

        // Use current user after pass authentication to validate access.
        if (!$this->currentUser->hasPermission('access content')) {
            throw new AccessDeniedHttpException();
        }
        $params = json_decode($this->currentRequest->getContent(), TRUE);
        if(isSet($params["execute"])){
            $stage = \Drupal::EntityTypeManager()->getStorage("stage")->load($id);
            // check if user is owner of stage
            if($stage->getOwnerId() != $this->currentUser->id()){
                throw new AccessDeniedHttpException();
            }

            // trigger events
            try{
                $stage->actions->offsetGet($params["execute"])->entity->triggerEvents();
            } catch(Exception $e) {
                return new ResourceResponse(["Error" => $e->getMessage()], 400);
            }
        }

        //response
        $res = ["id" => $id, "data" => $params];
        return new ModifiedResourceResponse($res, 201);
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
     * params: {
     *
     *   content_workflow id
     *   stage_action index
     * }
     */
    public function post($data = []) {

        $params = json_decode($this->currentRequest->getContent(), TRUE);
        if(isSet($params["content_workflow_id"]) && isSet($params["stage_action_ind"])){
            $content_workflow = \Drupal::EntityTypeManager()->getStorage("content_workflow")->load($params["content_workflow_id"]);
            $res = $content_workflow->transitionStage($params["stage_action_ind"], $this->currentUser->id());
            // $current_stage_index = $content_workflow->current_stage->value;
            // if($current_stage_index == null){
            //     return new ResourceResponse("Workflow has not been activated yet", 400);
            // }
            // $stage = $content_workflow->stages->offsetGet($current_stage_index)->entity;
            // $current_stage_instance = $stage->stage_instances->offsetGet($stage->stage_instances->count()-1)->entity;

            // // check if user is owner of stage
            // if($current_stage_instance->getOwnerId() != $this->currentUser->id()){
            //     throw new AccessDeniedHttpException();
            // }

            // try{
            //     $action = $stage->actions->offsetGet($params["stage_action_ind"])->entity;

            //     // check if action can be used
            //     if($action->uses->value <= $action->records->count()){
            //         return new ResourceResponse("Action has reached number of uses");
            //     }
            //     $action->uses->value = $action->uses->value - 1;
            //     $action->save();
            //     $target_stage_index = null;
            //     // trigger events
            //     $action->triggerEvents();

            //     $target_stage = $action->target_stage->entity;

            //     //in case target_stage is completion
            //     if($target_stage !== null && $target_stage->id() == $content_workflow->final_stage->target_id){
            //         $current_stage_instance->status = 2;
            //         $current_stage_instance->complete_date->value = StageInstance::DDTtoDTI(new \Drupal\Core\DateTime\DrupalDateTime());
            //         $current_stage_instance->save();
            //         $content_workflow->current_stage = -2;
            //         $content_workflow->save();
            //         return new ResourceResponse([]);
            //     }
            //     // figure out where the target stage appears in relation to the current stage in the stage order and modify linked list
            //     if($target_stage !== null){
            //         for($i = 0; $i < $content_workflow->stages->count(); $i++){
            //             if($content_workflow->stages->offsetGet($i)->target_id == $target_stage->id()){
            //                 $target_stage_index = $i;
            //             break;
            //             }
            //         }
            //     } else {
            //         // in the event that no target stage is given, assume it is the next stage
            //         $target_stage_index = $current_stage_index + 1;
            //     }
            //     //if the target stage is the same as the current stage, return without doing any stage transitions
            //     if($target_stage_index == $current_stage_index){
            //         return "No stage transition performed";
            //     }
            //     //if the target stage appears before the current stage create copies of the stages between to lead back to the current stage
            //     if($target_stage_index < $current_stage_index){
            //         $old_next_stage = $current_stage_instance->next_stage->entity;
            //         $target_stage_instance = $target_stage->createInstance(null, $current_stage_instance);
            //         $target_stage_instance->save();
            //         $current_stage_instance->next_stage = $target_stage_instance;
            //         $current_stage_instance->save();
            //         $prev_stage_instance = $target_stage_instance;
            //         $stage_instance = null;
            //         for($i = $target_stage_index + 1; $i <= $current_stage_index; $i++){
            //             $stage_instance = $content_workflow->stages[$i]->entity->createInstance(null, $prev_stage_instance);
            //             $stage_instance->save();
            //             $prev_stage_instance = $stage_instance;
            //         }
            //         $stage_instance->next_stage = $old_next_stage;
            //         $stage_instance->save();
            //         $old_next_stage->prev_stage = $stage_instance;
            //         $old_next_stage->save();
            //     }
            //     // stage transition for pushing passed the next stage to an upcoming stage
            //     elseif($target_stage_index > $current_stage_index + 1){
            //         $stage_instance = $current_stage_instance->next_stage->entity;
            //         while($stage_instance->stage_template->target_id !== $target_stage->id()){
            //             $prev_stage_instance = $stage_instance;
            //             $stage_instance = $stage_instance->next_stage->entity;
            //             $prev_stage_instance->delete();
            //         }
            //         $stage_instance->prev_stage = $current_stage_instance;
            //         $stage_instance->save();
            //         $current_stage_instance->next_stage = $stage_instance;
            //         $current_stage_instance->save();
            //     }

            //     // if the target stage index appears right after the current stage index do a basic stage transition
            //     if($target_stage_index == ($current_stage_index + 1)){
            //         //if current stage is last stage go to completion stage
            //         if($current_stage_index == $content_workflow->stages->count() - 1){
            //             $current_stage_instance->status = 2;
            //             $current_stage_instance->complete_date->value = StageInstance::DDTtoDTI(new \Drupal\Core\DateTime\DrupalDateTime());
            //             $current_stage_instance->save();
            //             $content_workflow->current_stage = -2;
            //             $content_workflow->save();

            //             return new ResourceResponse([]);
            //         }
            //     }

            // } catch(\Exception $e) {
            //     return new ResourceResponse(["Error" => $e->getMessage()], 400);
            // }

            // // update the status and completion date of the current and next stage
            // $record = $action->createRecord($this->currentUser->id());
            // $next_stage_instance = $current_stage_instance->next_stage->entity;
            // $current_stage_instance->action_record = $record;
            // $current_stage_instance->status->value = 2;
            // $current_stage_instance->complete_date->value = StageInstance::DDTtoDTI(new \Drupal\Core\DateTime\DrupalDateTime());
            // $current_stage_instance->save();
            // $next_stage_instance->status->value = 1;
            // $next_stage_instance->save();
            // $content_workflow->set("current_stage", $target_stage_index);
            // $content_workflow->save();
            // //update due dates
            // $current_stage_instance->cascadeDueDates(true);
            return new ResourceResponse($res);
        }

        //response

        return new ResourceResponse("bad input", 400);
    }

}
