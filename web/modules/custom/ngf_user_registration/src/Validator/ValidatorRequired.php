<?php

namespace Drupal\ngf_user_registration\Validator;

/**
 * Class ValidatorRequired.
 *
 * @package Drupal\ngf_user_registration\Validator
 */
class ValidatorRequired extends BaseValidator {

  /**
   * {@inheritdoc}
   */
  public function validates($value) {
    return is_array($value) ? !empty(array_filter($value)) : !empty($value);
  }

}
