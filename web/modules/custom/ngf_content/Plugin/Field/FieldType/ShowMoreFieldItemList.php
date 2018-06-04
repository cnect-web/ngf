<?php

namespace Drupal\ngf_user_profile\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Represents a configurable user full name field.
 */
class FullNameFieldItemList extends FieldItemList {

  use ComputedItemListTrait;

  /**
   * {@inheritdoc}
   */
  protected function computeValue() {
    $entity = $this->getEntity();
    $value = $entity->get('field_ngf_first_name')->value . ' ' . $entity->get('field_ngf_last_name')->value;

    $this->list[0] = $this->createItem(0, $value);
  }

}
