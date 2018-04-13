<?php

namespace Drupal\ngf_user_profile;


trait FlagTrait {
  protected function getFollowUserFlag() {
    return $this->getFlag('ngf_follow_user');
  }

  protected function getListItemFlag() {
    return $this->getFlag('ngf_list_item');
  }

  protected function getFlag($flag_id) {
    $flag = $this->flag->getFlagById($flag_id);
    if (empty($flag)) {
      throw new \LogicException(t('Flag @flag_id is not found.', ['@flag_id' => $flag_id]));
    }
    return $flag;
  }

}