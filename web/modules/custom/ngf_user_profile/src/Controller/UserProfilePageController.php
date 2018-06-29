<?php

namespace Drupal\ngf_user_profile\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\views\Views;
use Drupal\user\Entity\AccountInterface;
use Drupal\ngf_user_profile\Manager\UserFeedManager;
use Drupal\ngf_user_profile\Manager\UserManager;
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

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * User manager.
   *
   * @var \Drupal\ngf_user_profile\Manager\UserManager
   */
  protected $userManager;

  protected $currentUserAccount = NULL;

  public function __construct(
    AccountProxy $current_user,
    UserFeedManager $user_feed_manager,
    EntityTypeManagerInterface $entityTypeManager,
    FormBuilderInterface $formBuilder,
    UserManager $userManager
  ) {
    $this->currentUser = $current_user;
    $this->userFeedManager = $user_feed_manager;
    $this->entityTypeManager = $entityTypeManager;
    $this->formBuilder = $formBuilder;
    $this->userManager = $userManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('ngf_user_profile.user_feed_manager'),
      $container->get('entity_type.manager'),
      $container->get('form_builder'),
      $container->get('ngf_user_profile.user_manager')
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
  public function publications(EntityInterface $user = NULL) {
    return $this->getViewContent('publications', $user);
  }

  /**
   * {@inheritdoc}
   */
  public function events(EntityInterface $user = NULL) {
    return $this->getViewContent('events', $user);
  }

  /**
   * {@inheritdoc}
   */
  public function groups(EntityInterface $user = NULL) {
    return $this->getViewContent('groups', $user);
  }

  /**
   * {@inheritdoc}
   */
  public function followers(EntityInterface $user = NULL) {
    return $this->getContent($this->getUserList($this->userManager->getFollowersUsersList($user)), $user);
  }

  /**
   * {@inheritdoc}
   */
  public function following(EntityInterface $user = NULL) {
    return $this->getContent($this->getUserList($this->userManager->getFollowingUsersList($user)), $user);
  }

  protected function getUserList($users) {
    $items = [];
    foreach ($users as $user) {
      $items[] = $this->entityTypeManager->getViewBuilder('user')->view($user, 'compact');
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#attributes' => [
        'class' => [
          'profile__following',
        ],
      ],
    ];
  }

  public function contact(EntityInterface $user) {
    $message = $this
      ->entityTypeManager
      ->getStorage('contact_message')
      ->create(array(
        'contact_form' => 'personal',
        'recipient' => $user
          ->id(),
      ));
    $form = $this
      ->entityFormBuilder()
      ->getForm($message);

    return $this->getContent($form);
  }

  /**
   * {@inheritdoc}
   */
  public function generalSettings() {
    return $this->getContent($this->getEntityForm('default'));
  }

  /**
   * {@inheritdoc}
   */
  public function privateSettings() {
    return $this->getContent($this->formBuilder->getForm('Drupal\ngf_user_profile\Form\UserPrivateSettingsForm'));
  }

  /**
   * {@inheritdoc}
   */
  public function locationSettings() {
    return $this->getContent($this->formBuilder->getForm('Drupal\ngf_user_profile\Form\UserLocationSettingsForm'));
  }

  /**
   * {@inheritdoc}
   */
  public function interestsSettings() {
    return $this->getContent($this->getEntityForm('ngf_interests'));
  }

  /**
   * {@inheritdoc}
   */
  public function about(EntityInterface $user) {
    return $this->getContent($this->getUserDisplay($user, 'ngf_about'));
  }

  public function getEntityForm($form_view_mode, $entity_type = 'user') {
    $form = $this->entityTypeManager
      ->getFormObject($entity_type, $form_view_mode)
      ->setEntity($this->getCurrentUserAccount());

    return $this->formBuilder->getForm($form);
  }


  public function getContent($content, $user = NULL) {
    return [
      'header' => $this->getUserDisplay($user ?? $this->getCurrentUserAccount(), 'ngf_profile'),
      'tabs' => $this->getTabs(),
      'content' => $content,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getUserDisplay(EntityInterface $entity, $view_mode = 'ngf_profile') {
    $view_builder = $this->entityTypeManager->getViewBuilder('user');
    return $view_builder->view($entity, $view_mode);
  }

  /**
   * {@inheritdoc}
   */
  public function getView($view_name, $display_name, $user_id) {
    // Add the view block.
    $view = Views::getView($view_name);
    $view->setDisplay($display_name);
    $view->setArguments([$user_id]);
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

    // Add the view to the render array.
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
    $render['content']['#prefix'] = '<div class="newsfeed"><div class="view-content">';
    $render['content']['#suffix'] = '</div></div>';
    foreach ($result as $item) {
      $message = $this->entityTypeManager->getViewBuilder('message')->view($item, 'full');
      // There is a bug partial is still displayed even it's hidden in the view mode.
      unset($message['partial_0']);
      $render['content'][] = $message;
    }
    $render['content'][] = [
      '#type' => 'pager',
    ];
    return $this->getContent($render);
  }

  public function getViewContent($content_name, EntityInterface $user = NULL) {
    $prefix = !empty($user) ? 'user_' : 'your_';
    return $this->getContent($this->getView(
      'ngf_user_' . $content_name,
      $prefix . $content_name,
      !empty($user) ? $user->id() : $this->currentUser->id()
    ), $user);
  }

}
