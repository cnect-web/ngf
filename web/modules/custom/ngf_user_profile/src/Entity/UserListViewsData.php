<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for User list item entities.
 */
class UserListViewsData extends EntityViewsData {

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
