<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining User list item entities.
 *
 * @ingroup ngf_user_profile
 */
interface UserListItemInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the User list item creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User list item.
   */
  public function getCreatedTime();

  /**
   * Sets the User list item creation timestamp.
   *
   * @param int $timestamp
   *   The User list item creation timestamp.
   *
   * @return \Drupal\ngf_user_profile\Entity\UserListItemEntityInterface
   *   The called User list item entity.
   */
  public function setCreatedTime($timestamp);
}
