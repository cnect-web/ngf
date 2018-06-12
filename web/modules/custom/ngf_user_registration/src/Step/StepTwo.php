<?php

namespace Drupal\ngf_user_registration\Step;

use Drupal\ngf_user_registration\Button\StepTwoNextButton;
use Drupal\ngf_user_registration\Button\StepTwoPreviousButton;
use Drupal\ngf_user_registration\Validator\ValidatorRequired;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StepTwo.
 *
 * @package Drupal\ngf_user_registration\Step
 */
class StepTwo extends BaseStep {

  /**
   * {@inheritdoc}
   */
  protected function setStep() {
    return StepsEnum::STEP_TWO;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      new StepTwoPreviousButton(),
      new StepTwoNextButton(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildStepFormElements(FormStateInterface $form_state) {
    $form['country'] = array(
      '#type' => 'entity_autocomplete',
      '#target_type' => 'taxonomy_term',
      '#selection_settings' => [
        'include_anonymous' => FALSE,
        'target_bundles' => array('ngf_countries'),
      ],
      '#default_value' => !empty($this->getValues()['country']) ? $this->getValues()['country'] : NULL,
      '#ajax' => [
        'callback' => [get_class($this), 'getCity'],
        'event' => 'change',
        'wrapper' => 'city-wrapper',
      ],
    );


    $form['city_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'city-wrapper'],
    ];

    if ($country = $form_state->getValue('country')) {
      $form['city_wrapper']['name'] = array(
        '#type' => 'textfield',
        '#autocomplete_route_name' => 'ngf_user_registration.city_autocomplete',
        //      '#autocomplete_route_parameters' => array('field_name' => 'name', 'count' => 10),
      );
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldNames() {
    return [
      'city',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsValidators() {
    return [
      'city' => [
//        new ValidatorRequired("It would be a lot easier for me if you could fill out some of your interests."),
      ],
    ];
  }

  public static function getCity(&$form, FormStateInterface $form_state) {
    return $form['wrapper']['city_wrapper'];
  }

}
