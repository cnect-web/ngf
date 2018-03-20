<?php

namespace Drupal\ngf_core\EventSubscriber;

use Drupal\social_auth\Event\SocialAuthUserFieldsEvent;
use Drupal\social_auth\Event\SocialAuthUserEvent;
use Drupal\social_auth\Event\SocialAuthEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Reacts on Social Auth events.
 */
class SocialAuthSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   *
   * Returns an array of event names this subscriber wants to listen to.
   * For this case, we are going to subscribe for user creation and login
   * events and call the methods to react on these events.
   */
  public static function getSubscribedEvents() {
    $events[SocialAuthEvents::USER_CREATED] = ['onUserCreated'];
    $events[SocialAuthEvents::USER_LOGIN] = ['onUserLogin'];

    return $events;
  }

  /**
   * Alters the user name.
   *
   * @param \Drupal\social_auth\Event\SocialAuthUserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserCreated(SocialAuthUserEvent $event) {
    $ddumper = \Drupal::service('devel.dumper');
    $ddumper->debug($event);
  }

  /**
   * Sets a drupal message when a user logs in.
   *
   * @param \Drupal\social_auth\Event\SocialAuthUserEvent $event
   *   The Social Auth user event object.
   */
  public function onUserLogin(SocialAuthUserEvent $event) {
    $ddumper = \Drupal::service('devel.dumper');
    $ddumper->debug($event);
  }

}

