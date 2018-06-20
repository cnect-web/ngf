<?php

namespace Drupal\ngf_group\Controller;

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

  /**
   * {@inheritdoc}
   */
  public function groupInfo(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group.
    $render_array['header'] = $this->groupHeader($group, 'full');

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function publicationsPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    // Add the view block.
    $render_array['content'] = $this->getContentView('ngf_group_publications', 'discussions', $group->id());

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function eventsPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    // Add the view block.
    $render_array['content'] = $this->getContentView('ngf_group_events', 'events', $group->id());

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function libraryPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    // Add the view block.
    $render_array['content'] = $this->getContentView('ngf_group_library', 'library', $group->id());

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function sharedContentPage(EntityInterface $group) {
    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);
    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function membersPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    // Add the view block.
    $render_array['content'] = $this->getContentView('ngf_group_members', 'members', $group->id());

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function followersPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    // Add the view block.
    $render_array['content'] = $this->getContentView('ngf_group_followers', 'followers', $group->id());

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function subgroupsPage(EntityInterface $group) {

    $gD = new NGFGroup($group);

    // Add the group header.
    $render_array['header'] = $this->groupHeader($group);

    // Add the group tabs.
    $render_array['group_tabs'] = $gD->getGroupTabs();

    // Add the view block.
    $render_array['content'] = $this->getContentView('ngf_group_subgroups', 'subgroups', $group->id());

    return $render_array;
  }

  /**
   * {@inheritdoc}
   */
  public function groupHeader(EntityInterface $group, $view_mode = 'header') {
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
