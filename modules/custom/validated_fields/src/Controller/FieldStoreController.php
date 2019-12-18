<?php

namespace Drupal\validated_fields\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\validated_fields\FieldStoreTypeInterface;

class FieldStoreController extends ControllerBase{
  public function add( $field_store_type){
    $field_store_types = $this->entityManager()->getStorage('field_store_type')->loadbyProperties(['label' => $field_store_type]);
    $field_store_typ = '';
    foreach($field_store_types as $key => $val){
      $field_store_typ = $val;
      break;
    }
    $field_store = $this->entityManager()->getStorage('field_store')->create(array('type' => $field_store_typ->id()));
    $form = $this->entityFormBuilder()->getForm($field_store);
    return $form;
  }

  public function addPage(){
    $content = array('#rows' => array());
    foreach(\Drupal::EntityTypeManager()->getStorage('field_store_type')->loadMultiple() as $type){
      array_push($content['#rows'] ,  array( 'data' => array(
         '#title' => $type->label,
         '#type' => 'link',
         '#url' => \Drupal\Core\Url::fromRoute("validated_fields.field_store_add",["field_store_type" => $type->id()]),
      )));
    }
    $content['#title'] = "Types:";
    $content['#type'] = 'table';
    $content['#header'] =  array('type');
    return array(
      'link' => $content,
    );
  }
}
?>
