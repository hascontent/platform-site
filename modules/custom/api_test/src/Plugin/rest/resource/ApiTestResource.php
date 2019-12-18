<?php

namespace Drupal\api_test\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Component\Serialization\Json;

/**
 * Provides a Demo Resource
 *
 * @RestResource(
 *   id = "api_test_resource",
 *   label = @Translation("Api Test Resource"),
 *   uri_paths = {
 *     "canonical" = "/api-test/resource/{testvar}",
 *     "create" = "//api/custom"
 *   }
 * )
 */
class ApiTestResource extends ResourceBase {
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($testvar,$var) {
    $response = ['message' => 'Hello, this is a rest sauce'];
    $tst = Json::decode($var);
    $check = var_export($var, true);
    if($var){
      $response['first var'] = $check;
    } else {
      $response['epic'] = 'fail';
    }
    $response['slug'] = $testvar;
    return new ResourceResponse($response);
  }
  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function post($var){
    $response = ['message' => 'Hello, this is a rest sauce'];
    $tst = $var;
    $check = var_export($tst, true);
    if($tst){
      $response['first var'] = $tst;
    } else {
      $response['epic'] = 'fail';
    }
    return new ResourceResponse($response);
  }
}

?>
