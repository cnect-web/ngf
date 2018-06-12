<?php

namespace Drupal\ngf_user_registration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\message\Entity\Message;
use Drupal\message\Entity\MessageTemplate;
use Drupal\ngf_user_profile\Helper\UserHelper;
use Drupal\ngf_user_profile\Manager\UserFeedManager;
use Drupal\ngf_user_profile\Manager\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ngf_user_profile\UserFeedItem;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class UserRegistrationController.
 */
class UserRegistrationController extends ControllerBase {

  /**
   * Profile.
   *
   * @return string
   *   Return Hello string.
   */
  public function citiesAutocomplete() {
    $matches = [];
    $matches[] = array('value' => 'go1', 'label' => 'go1');
    $matches[] = array('value' => 'my1', 'label' => 'my1');
    $matches[] = array('value' => 'pas1', 'label' => 'pas1');
    return new JsonResponse($matches);
  }

}
