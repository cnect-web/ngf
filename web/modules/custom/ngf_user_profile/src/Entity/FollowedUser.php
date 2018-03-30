<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Followed user entity.
 *
 * @ingroup ngf_user_profile
 *
 * @ContentEntityType(
 *   id = "ngf_followed_user",
 *   label = @Translation("Followed user"),
 *   base_table = "ngf_followed_user",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "followed_user_id" = "followed_user_id",
 *   },
 * )
 */
class FollowedUser extends ContentEntityBase implements FollowedUserInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(
    EntityStorageInterface $storage_controller,
    array &$values
  ) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFollowedUserId() {
    return $this->get('followed_user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setFollowedUserId($followed_user_id) {
    $this->set('followed_user_id', $followed_user_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFollowedId() {
    return $this->get('followed_user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setFollowedId($uid) {
    $this->set('followed_user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFollowedUser() {
    return $this->get('followed_user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type
  ) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Current user'))
      ->setDescription(t('The user ID of current user.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('view', TRUE);

    $fields['followed_user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Followed user ID'))
      ->setDescription(t('The followed user ID.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entry was created.'));

    return $fields;
  }

}
