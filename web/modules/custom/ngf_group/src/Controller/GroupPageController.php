<?php

namespace Drupal\ngf_group\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\ngf_group\Entity\Decorator\NGFGroup;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\views\Views;

/**
 * Discover page controller.
 */
class GroupPageController extends ControllerBase {

  public function accessCheck(EntityInterface $group) {
    if ($group->bundle() == 'ngf_discussion_group') {
      return AccessResult::allowed();
    }
    return AccessResult::forbidden();
  }

  /**
   * {@inheritdoc}
   */
  public function groupInfo(EntityInterface $group) {
    return $this->getPageContent($group, $this->groupDisplay($group, 'ngf_about'));
  }

  /**
   * {@inheritdoc}
   */
  public function publicationsPage(EntityInterface $group) {
    return $this->getViewContent($group, 'publications');
  }

  /**
   * {@inheritdoc}
   */
  public function eventsPage(EntityInterface $group) {
    return $this->getViewContent($group, 'events');
  }

  /**
   * {@inheritdoc}
   */
  public function libraryPage(EntityInterface $group) {
    return $this->getViewContent($group, 'library');
  }

  /**
   * {@inheritdoc}
   */
  public function sharedContentPage(EntityInterface $group) {
    // Add the group header.
    return $this->getPageContent($group, [
      '#type' => 'markup',
      '#markup' => 'There is no shared content'
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function membersPage(EntityInterface $group) {
    return $this->getViewContent($group, 'members');
  }

  /**
   * {@inheritdoc}
   */
  public function followersPage(EntityInterface $group) {
    return $this->getViewContent($group, 'followers');
  }

  /**
   * {@inheritdoc}
   */
  public function subgroupsPage(EntityInterface $group) {
    return $this->getViewContent($group, 'subgroups');
  }

  public function getViewContent(EntityInterface $group, $page) {
    return $this->getPageContent($group, $this->getContentView('ngf_group_' . $page, $page, $group->id()));
  }

  /**
   *
   */
  public function getPageContent(EntityInterface $group, $content) {

    // Add the group.
    $render_array['header'] = $this->groupDisplay($group);

    if ($this->accessCheck($group) == AccessResult::allowed()) {
      $gD = new NGFGroup($group);
      // Add the group tabs.
      $render_array['group_tabs'] = $gD->getGroupTabs();

      // Add the view block.
      $render_array['content'] = $content;
    }

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function groupDisplay(EntityInterface $group, $view_mode = 'ngf_header') {
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('group');
    return $view_builder->view($group, $view_mode);
  }

  /**
   * {@inheritdoc}
   */
  public function getContentView($view_name, $display_name, $group_id) {

    // Add the view block.
    $view = Views::getView($view_name);
    $view->setDisplay($display_name);
    $view->setArguments([$group_id]);
    $view->preExecute();
    $view->execute();

    $render_array['view'] = [
      '#type' => 'container',
      '#tree' => TRUE,
    ];

    // Add the groups view title to the render array.
    $title = $view->getTitle();
    if ($title) {
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

}
