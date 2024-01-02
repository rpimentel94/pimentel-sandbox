<?php

namespace Drupal\htlf_alert\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'Alert' Block.
 *
 * @Block(
 *   id = "htlf_alert_block",
 *   admin_label = @Translation("Alert block"),
 *   category = @Translation("HTLF Custom Block"),
 * )
 */
class AlertsBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    //Get Active Domain
    $active_domain = \Drupal::service('domain.negotiator')->getActiveId();

    //Get current path & check for alias URL
    $current_path = \Drupal::service('path.current')->getPath();
    $alias = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
    $is_front = \Drupal::service('path.matcher')->isFrontPage();
    $alias = $is_front ? 'home' : $alias;
    $location = NULL;


    //Determine which alert types are needed
    switch ($alias) {
      case 'home':
        $location = ['global', 'home'];
        break;
      case '/locations':
        $location = ['locations-main', 'global'];
        break;
      case str_contains($alias, '/locations/'):
        $location = ['locations-all', 'global'];
        break;
      default:
        $location = ['global'];
        break;
    }

    $nids = NULL;

    //Get All Alerts that meet criteria
    $query = \Drupal::entityQuery('node');
    $group = $query
      ->orConditionGroup()
      ->condition('field_domain_all_affiliates', 1) //If allowed on all domains
      ->condition('field_domain_access', $active_domain, "="); //Match the current domain

    $nids = $query->condition($group)
      ->condition('type', 'alert')
      ->condition('field_alert_location', $location, 'IN')
      ->condition('status', 1)
      ->accessCheck(TRUE)
      ->execute();

    $nids = $query->execute();

    //Load All Alert Nodes Found
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    $alerts = [];

    if ($nodes) {
      foreach ($nodes as $node) {
        $item = [];
        $item['nid'] = $node->id();

        $item['body'] = [
          '#type' => 'processed_text',
          '#text' => $node->get('body')->value,
          '#format' => $node->get('body')->format,
        ];

        $item['alert_level'] = $node->get('field_alert_theme')->getString();
        $alerts[$node->id()] = $item;
      }
    }

    return [
      '#theme' => 'htlf_alert_block',
      '#alerts' => $alerts,
      '#attached' => [
        'library' => ['tailwind/alert-messages'],
      ],
    ];
  }


}