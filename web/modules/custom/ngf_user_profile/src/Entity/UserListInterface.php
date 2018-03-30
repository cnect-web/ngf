<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User list entities.
 *
 * @ingroup ngf_user_profile
 */
interface UserListInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the User list creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User list.
   */
  public function getCreatedTime();

  /**
   * Sets the User list creation timestamp.
   *
   * @param int $timestamp
   *   The User list creation timestamp.
   *
   * @return \Drupal\ngf_user_profile\Entity\UserListEntityInterface
   *   The called User list entity.
   */
  public function setCreatedTime($timestamp);
}
