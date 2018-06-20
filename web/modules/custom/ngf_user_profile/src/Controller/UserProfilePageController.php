<?php

namespace Drupal\ngf_user_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\views\Views;
use Drupal\ngf_user_profile\Controller\AccountInterface;
use Drupal\ngf_user_profile\Manager\UserFeedManager;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Discover page controller.
 */
class UserProfilePageController extends ControllerBase {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * The user feed manager service.
   *
   * @var Drupal\ngf_user_profile\Manager\userFeedManager
   */
  protected $userFeedManager;

  protected $currentUserAccount = NULL;

  public function __construct(AccountProxy $current_user, UserFeedManager $user_feed_manager) {
    $this->currentUser = $current_user;
    $this->userFeedManager = $user_feed_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('ngf_user_profile.user_feed_manager')
    );
  }

  protected function getCurrentUserAccount() {
    if (empty($this->currentUserAccount)) {
      $this->currentUserAccount = User::load($this->currentUser->id());
    }
    return $this->currentUserAccount;
  }

  /**
   * {@inheritdoc}
   */
  public function userInfo() {
    return $this->getContent('');
  }

  /**
   * {@inheritdoc}
   */
  public function publications() {
    return $this->getContent($this->getContentView('ngf_my_publications', 'publications'));
  }

  /**
   * {@inheritdoc}
   */
  public function events() {
    return $this->getContent($this->getContentView('ngf_user_events', 'events'));
  }

  /**
   * {@inheritdoc}
   */
  public function groups() {

    return $this->getContent($this->getContentView('ngf_user_groups', 'groups'));
  }

  /**
   * {@inheritdoc}
   */
  public function followers() {
    return $this->getContent($this->getContentView('ngf_followers', 'followers'));
  }

  /**
   * {@inheritdoc}
   */
  public function following() {
    return $this->getContent($this->getContentView('ngf_following', 'following'));
  }

  /**
   * {@inheritdoc}
   */
  public function generalSettings() {
    return $this->getContent(
      [
        '#type' => 'markup',
        '#markup' => 'General settings page'
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  public function privateSettings() {
    var_dump(Drupal\ngf_user_profile\Form\UserPrivateSettingsForm::class);
    $form = \Drupal::formBuilder()->getForm('Drupal\ngf_user_profile\Form\UserPrivateSettingsForm');
    return $this->getContent($form);
  }

  /**
   * {@inheritdoc}
   */
  public function locationSettings() {
    return $this->getContent([
      '#type' => 'markup',
      '#markup' => 'Location settings page'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function interestsSettings() {
    return $this->getContent([
      '#type' => 'markup',
      '#markup' => 'Interests settings page'
    ]);
  }


  public function getContent($content) {
    return [
      'header' => $this->getHeader($this->getCurrentUserAccount()),
      'tabs' => $this->getTabs(),
      'content' => $content,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getHeader(EntityInterface $entity, $view_mode = 'header') {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('user');
    return $view_builder->view($entity, $view_mode);
  }

  /**
   * {@inheritdoc}
   */
  public function getContentView($view_name, $display_name) {

    // Add the view block.
    $view = Views::getView($view_name);
    $view->setDisplay($display_name);
    $view->setArguments([$this->currentUser->id()]);
    $view->preExecute();
    $view->execute();

    $render_array['view'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    // Add the groups view title to the render array.
    if ($title = $view->getTitle()) {
      $render_array['view']['title'] = [
        '#type' => 'html_tag',
        '#tag' => 'h2',
        '#value' => $title,
      ];
    }

    // Add the groups view to the render array.
    $render_array['view']['content'] = $view->render();

    return $render_array['view'];

  }

  /**
   * Return the tabs.
   */
  public function getTabs() {
    $block_manager = \Drupal::service('plugin.manager.block');
    $config = [
      'primary' => FALSE,
      'secondary' => TRUE
    ];
    $plugin_block = $block_manager->createInstance('local_tasks_block', $config);
    $access_result = $plugin_block->access($this->currentUser);
    if (is_object($access_result) && $access_result->isForbidden() || is_bool($access_result) && !$access_result) {
      return [];
    }

    $render['wrapper'] = [
      '#type' => 'html_tag',
      '#tag' => 'nav',
      '#attributes' => [
        'class' => [
          'inpage-nav',
        ]
      ],
      'tabs' => $plugin_block->build(),
    ];

    ksm($render);

    return $render;
  }

  public function feed() {

    $publications = $this->userFeedManager->getContent();
    $page = pager_find_page();
    $num_per_page = 10;
    $offset = $num_per_page * $page;
    $result = array_slice($publications, $offset, $num_per_page);

    // Now that we have the total number of results, initialize the pager.
    pager_default_initialize(count($publications), $num_per_page);

    // Create a render array with the search results.
    $render = [];

    $items = [];
    foreach ($result as $item) {
      $message = \Drupal::entityTypeManager()->getViewBuilder('message')->view($item, 'full');
      // There is a bug partial is still displayed even it's hidden in the view mode.
      unset($message['partial_0']);
      $items[] = $message;
    }

    $render[] = [
      '#theme' => 'item_list',
      '#items' => $items
    ];
    $render[] = [
      '#type' => 'pager',
    ];
    return $this->getContent($render);
  }

}
