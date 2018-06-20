<?php

namespace Drupal\ngf_user_profile\Form;

use Drupal\Component\Utility\Tags;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ngf_user_registration\Manager\StepManager;
use Drupal\ngf_user_registration\Step\StepsEnum;
use Drupal\redirect\Entity\Redirect;
use Drupal\user\RoleInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\ngf_user_profile\Manager\UserSettingsManager;
use Drupal\user\UserData;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides location settings form.
 *
 * @package Drupal\ngf_user_registration\Form
 */
class UserLocationSettingsForm extends FormBase {
  use StringTranslationTrait;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Class constructor.
   */
  public function __construct(AccountInterface $currentUser) {
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ngf_user_location_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $country = $this->getValue('country', $form_state->getValue('country'));
    $form['title'] = [
      '#type' => 'item',
      '#markup' => t('<h2>Location settings</h2>'),
    ];
    $form['country'] = [
      '#title' => t('Country'),
      '#type' => 'select',
      '#options' => $this->getCountryOptions(),
      '#default_value' => $country,
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

    $city = $this->getValue('city');
    if (!empty($city) || !empty($country)) {
      $form['city_wrapper']['city'] = [
        '#type' => 'textfield',
        '#title' => t('City'),
        '#default_value' => $city,
        '#autocomplete_route_name' => 'ngf_user_registration.city_autocomplete',
        '#autocomplete_route_parameters' => ['country_id' => $country],
        '#attributes' => [
          'class' => [
            'form__input form__input--text',
          ]
        ],
      ];
    }

    return $form;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {


    \Drupal::getContainer()->get('messenger')->addMessage(t('Your location settings have been updated'));
  }

  public static function getCity(&$form, FormStateInterface $form_state) {
    return $form['wrapper']['city_wrapper'];
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
