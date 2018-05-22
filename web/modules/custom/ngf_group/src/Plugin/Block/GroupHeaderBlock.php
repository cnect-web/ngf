<?php

namespace Drupal\ngf_group\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityDisplayRepository;

/**
 * Provides a 'GroupHeaderBlock' block.
 *
 * @Block(
 *   id = "group_header_block",
 *   admin_label = @Translation("Group Header block"),
 *   context = {
 *     "group" = @ContextDefinition(
 *       "entity:group",
 *       label = @Translation("Group"),
 *       required = TRUE
 *     ),
 *     "node" = @ContextDefinition(
 *       "entity:node",
 *       label = @Translation("Current Node"),
 *       required = FALSE
 *     )
 *   }
 * )
 */
class GroupHeaderBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();

    $default_view_mode = $config['view_mode'] ?? 'default';
    $view_modes_mn = \Drupal::service('entity_display.repository')->getViewModes('group');

    $view_modes = [];
    foreach ($view_modes_mn as $key => $view_mode) {
      $view_modes[$key] = $view_mode['label'] ?? 'Default';
    }

    $form['view_mode'] = [
      '#title' => 'View mode',
      '#type' => 'select',
      '#options' => $view_modes,
      '#default_value' => $default_view_mode
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['view_mode'] = $values['view_mode'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    // Get the group from current context.
    if (($group = $this->getContextValue('group')) && $group->id()) {
      // Get the node from current context.
      if (($node = $this->getContextValue('node')) && $node->id()) {
        $entity_type = $group->getEntityType()->id();
        $entity_id = $group->id();
        $config = $this->getConfiguration();
        $view_mode = $config['view_mode'] ?? 'default';

        $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
        $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
        $pre_render = $view_builder->view($entity, $view_mode);
        $build['#markup'] = render($pre_render);
      }

    }
    return $build;
  }

}
