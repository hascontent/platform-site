<?php

namespace Drupal\api_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Url;

use Drupal\redirect\Entity\Redirect;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field_validation\FieldValidationRuleManager;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\NodeType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\Node;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Component\Serialization\Json;

/**
 * Returns responses for Api Test routes.
 */
class ApiTestController extends ControllerBase {

  /**
   * The fieldValidationRule manager service. Used to query all possible
   * field validation rules
   *
   * @var \Drupal\field_validation\FieldValidationRuleManager
   */
  // protected $fieldValidationRuleManager;
  /**
   * Entity Type Manager Service. Used to get CRUD operations on entity types
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  protected $entity_type_manager;
  /**
   * Constructs an FieldValidationRuleSetEditForm object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The storage.
   * @param \Drupal\field_validation\FieldValidationRuleManager $field_validation_rule_manager
   *   The field_validation_rule manager service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entity_type_manager = $entity_type_manager;
  }


  public static function create(ContainerInterface $container){
    return new static(
      $container->get('entity_type.manager')
    );
  }
  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  public function renderApi(){
    // $types = NodeType::loadMultiple();
    $types = $this->entity_type_manager->getStorage('node_type')->loadMultiple();
    $test = $types["js_test_1"];
    // $query = \Drupal::request()->request->get('insert key here'); //insert key here
    $field_storage = FieldStorageConfig::loadByName('node', '');
    $field = FieldConfig::loadMultiple();
    return new JsonResponse(Node::baseFieldDefinitions($types['article']));
  }

  public function postApiTest(){
    // return new RedirectResponse(Url::fromRoute('api_test.test')->toString());

    return new JsonResponse(
      [
        'data' => ['status' =>'epic success'],
      ]
    );
  }

  public function postFieldStorageConfig(){
    $fieldStorage = \Drupal::EntityTypeManager()->getStorage('field_storage_config')->create(array(
      "id" => "test_id_in_controller",   //forward compatible field_name
      "field_name" => "test_controller", // id/machine_name for field storage_config
      "type" => "boolean",  //field type, look at /jsonapi/field_storage_config/field_storage_config for examples
      "entity_type" => "node" //entity type on which fields apply to, will always be node for all content
    ));
    $status;
    try {
      $fieldStorage->save();
      $status = "try fully executed";
    } catch(\Drupal\Core\Entity\EntityStorageException $e){
      $status = "exception caught: " . $e->getMessage();
    }
    return new JsonResponse([
      'data' => $status
    ]);
  }

  public function fetchFieldStorageConfig(){
    $field_storages = \Drupal::EntityTypeManager()->getStorage('field_storage_config')->loadMultiple();
    return new JsonResponse($field_storages->toArray());
  }

  public function fetchFieldBundles(){
    $field_bundles = \Drupal::EntityTypeManager()->getStorage('node')->loadByProperties(['type' => 'field_bundle']);
    $paragraph_types = \Drupal::EntityTypeManager()->getStorage('paragraphs_type')->loadMultiple();
    $return = [];
    foreach($field_bundles as $key => $val){
      $return[$key] = [
        'id' => $key,
        'values'=>[],
        'title' => $val->title->getString()
      ];

      foreach($val->field_paragraph_type as $paragraph_link){
        $paragraph = $paragraph_types[$paragraph_link->getValue()['target_id']];
        $return[$key]['values'][$paragraph->id] = $paragraph;
      }
    }
    return new JsonResponse($return);
  }
  public function postFieldConfig(){
    $field_storage_config = \Drupal::EntityTypeManager()->getStorage('field_storage_config')->loadByProperties([
      'entity_type' => 'paragraph'
    ]);
    $return = [];
    foreach($field_storage_config as $key => $val){
      if($key == "paragraph.field_permission_level"){
        continue;
      }
      array_push($return, $val->toArray()['field_name']);
    }
    return new JsonResponse($return);
  }

  public function postNodeType(Request $request){
    $params = \Drupal::request()->request->all();
    $paramtest = Json::decode($request->getContent());
    return new JsonResponse(["raw" => var_export($params,true), "data" => $paramtest]);
  }

  public function sandBox(){
    $defs = \Drupal::service('plugin.manager.field.field_type')->getDefinitions();
    $base_field_types = \Drupal::EntityTypeManager()->getStorage('field_storage_config')->loadByProperties(["entity_type" => "node"]);
  }
}

