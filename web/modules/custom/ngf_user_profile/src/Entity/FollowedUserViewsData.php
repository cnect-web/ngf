<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for followed user entities.
 */
class FollowedUserViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
