<?php

namespace Drupal\ngf_user_profile\Manager;

class UserSettingsManager extends UserManager {


  public function getEntityType() {
    return '';
  }

  protected function setSetting($name, $value) {
    $this->userData->set('ngf_user_profile', $this->currentUser->id(), $name, $value);
  }

  protected function getSetting($name) {
    $this->userData->get('ngf_user_profile', $this->currentUser->id(), $name);
  }

  public function setActionContact() {
    $this->setSetting('action_contact');
  }

  public function getActionContact($value) {
    $this->setSetting('action_contact', $value);
  }

  public function setActionInviteToEvent() {
    $this->setSetting('action_invite_to_event');
  }

  public function getActionInviteToEvent($value) {
    $this->setSetting('action_invite_to_event', $value);
  }

  public function setActionInviteToGroup() {
    $this->setSetting('action_invite_to_group');
  }

  public function getActionInviteToGroup($value) {
    $this->setSetting('action_invite_to_group', $value);
  }

  public function setSearchEmail() {
    $this->setSetting('search_email');
  }

  public function getSearchEmail($value) {
    $this->setSetting('search_email', $value);
  }

  public function setSearchInterests() {
    $this->setSetting('search_interests');
  }

  public function getSearchInterests($value) {
    $this->setSetting('search_interests', $value);
  }

  public function setSearchLocation() {
    $this->setSetting('search_location');
  }

  public function getSearchLocation($value) {
    $this->setSetting('search_location', $value);
  }

  public function setSearchName() {
    $this->setSetting('search_name');
  }

  public function getSearchName($value) {
    $this->setSetting('search_name', $value);
  }

  public function setSearchPosition() {
    $this->setSetting('search_position');
  }

  public function getSearchPosition($value) {
    $this->setSetting('search_position', $value);
  }


}

