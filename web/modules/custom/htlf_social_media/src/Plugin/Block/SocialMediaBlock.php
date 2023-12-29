<?php

namespace Drupal\htlf_social_media\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides the 'Social Media Links' Block.
 *
 * @Block(
 *   id = "htlf_social_media_block",
 *   admin_label = @Translation("Social Media Links"),
 *   category = @Translation("HTLF Custom Block"),
 * )
 */
class SocialMediaBlock extends BlockBase
{

  /**
   * {@inheritdoc}
   */
  public function build()
  {
    //Get Active Domain
    $active_domain = \Drupal::service('domain.negotiator')->getActiveId() != "pimentel_sandbox_lndo_site" ?: "htlf";
    $accounts = [];

    $config_pages = \Drupal::service('config_pages.loader');
    $site_settings = $config_pages->load('site_settings');



    return [
      '#theme' => 'htlf_social_media_block',
      '#accounts' => $accounts,
    ];
  }


}