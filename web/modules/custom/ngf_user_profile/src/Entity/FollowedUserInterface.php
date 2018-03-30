<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining followed user entities.
 *
 * @ingroup ngf_user_profile
 */
interface FollowedUserInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the followed user creation timestamp.
   *
   * @return int
   *   Creation timestamp of the followed user.
   */
  public function getCreatedTime();

  /**
   * Sets the followed user creation timestamp.
   *
   * @param int $timestamp
   *   The followed user creation timestamp.
   *
   * @return \Drupal\ngf_user_profile\Entity\FollowedUserEntityInterface
   *   The called followed user entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the followed user id.
   *
   * @return int
   *   Folowed user id.
   */
  public function getFollowedUserId();

  /**
   * Sets the followed user id.
   *
   * @param int $followed_user_id
   *   The followed user id.
   *
   * @return \Drupal\ngf_user_profile\Entity\FollowedUserEntityInterface
   *   The called followed user entity.
   */
  public function setFollowedUserId($followed_user_id);
}
