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
//    $query = \Drupal::entityQuery('taxonomy_term');
//    $query->condition('vid', 'ngf_cities');
//    $query->condition('field_ngf_country', 1);
//    var_dump($query->execute());
//    exit();

    $country = $form_state->getValue('country');
    $form['country'] = [
      '#title' => t('Country'),
      '#type' => 'select',
      '#options' => $this->getCountryOptions(),
      '#default' => $country,
      '#ajax' => [
        'callback' => [get_class($this), 'getCity'],
        'event' => 'change',
        'wrapper' => 'city-wrapper',
      ],
    ];

    $form['city_wrapper'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'city-wrapper'],
    ];


    $city = $form_state->getValue('city');
    if (!empty($country) || !empty($city)) {
      $form['city_wrapper']['city'] = [
        '#type' => 'textfield',
        '#default' => $city,
        '#autocomplete_route_name' => 'ngf_user_registration.city_autocomplete',
        '#autocomplete_route_parameters' => ['country_id' => $country],
      ];
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

  public static function setCity(&$form, FormStateInterface $form_state) {
    $form_state->set('city', $form_state);
    $form_state->setRebuild();
  }

  public function getCountryOptions() {
    $vocabulary_name = 'ngf_countries';
    $countries = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vocabulary_name);
    $items = [];
    $items[0] = t('Select country');
    foreach ($countries as $country) {
      $items[$country->tid] = $country->name;
    }

    return $items;
  }

}
