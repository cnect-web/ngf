<?php

namespace Drupal\ngf_user_registration\Validator;

/**
 * Class ValidatorUsername.
 *
 * @package Drupal\ngf_user_registration\Validator
 */
class ValidatorUsername extends BaseValidator {
  /**
   * {@inheritdoc}
   */
  public function validates($value) {
    $error_message = user_validate_name($value);
    if (!empty($error_message)) {
      $this->setErrorMessage($error_message);
      return false;
    }

    return true;
  }

}
