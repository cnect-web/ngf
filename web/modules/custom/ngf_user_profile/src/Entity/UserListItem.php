<?php

namespace Drupal\ngf_user_profile\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the User list item entity.
 *
 * @ingroup ngf_user_profile
 *
 * @ContentEntityType(
 *   id = "ngf_user_list_item",
 *   label = @Translation("User list item"),
 *   base_table = "ngf_user_list_item",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "list_user_id" = "list_user_id",
 *     "list_id" = "list_id"
 *   },
 * )
 */
class UserListItem extends ContentEntityBase implements UserListItemInterface {

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
  public function getListUserId() {
    return $this->get('list_user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setListUserId($user_list_id) {
    $this->set('list_user_id', $user_list_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getListId() {
    return $this->get('list_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setListId($list_d) {
    $this->set('list_id', $list_d);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUser() {
    return $this->get('list_user_id')->entity;
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

    $fields['list_user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('List user item ID'))
      ->setDescription(t('The user ID of user of the list.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('view', TRUE);

    $fields['list_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('List ID'))
      ->setDescription(t('The list ID.'))
      ->setSetting('target_type', 'ngf_user_list')
      ->setSetting('handler', 'default')
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entry was created.'));

    return $fields;
  }

}
