<?php

namespace Drupal\oauth_logout\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class OauthLogoutController extends ControllerBase{
  
  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entity_manager;

  /**
   * Controller constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entity_manager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function logout(ServerRequestInterface $request){
    $user = $this->currentUser();
    if($user->id() === 0){
      return new Response(403,[],'resource not allowed to be used by anonymous users');
    }
    $tokens = $this->entity_manager->getStorage("oauth2_token")->loadByProperties(["auth_user_id" => $user->id()]);
    foreach($tokens as $token)
      $token->delete();
    return new Response(200,[],'logged out');
  }
}