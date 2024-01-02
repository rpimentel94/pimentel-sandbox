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
    $active_domain = \Drupal::service('domain.negotiator')->getActiveId();
    $accounts = [];

    $config_pages = \Drupal::service('config_pages.loader');
    $site_settings = $config_pages->load('site_settings');


    if ($site_settings->get('field_enable_social_media_icons') && $site_settings->get('field_enable_social_media_icons')->getString() == true ) {
      $accounts['facebook'] = $site_settings->get('field_facebook_url')->getString() ?: NULL;
      $accounts['instagram'] = $site_settings->get('field_instagram_url')->getString() ?: NULL;
      $accounts['linkedin'] = $site_settings->get('field_linkedin_url')->getString() ?: NULL;
      $accounts['pinterest'] = $site_settings->get('field_pinterest_url')->getString() ?: NULL;
      $accounts['twitter'] = $site_settings->get('field_twitter_url')->getString() ?: NULL;
      $accounts['yelp'] = $site_settings->get('field_yelp_url')->getString() ?: NULL;
      $accounts['youtube'] = $site_settings->get('field_youtube_url')->getString() ?: NULL;
    }

    return [
      '#theme' => 'htlf_social_media_block',
      '#accounts' => $accounts,
    ];
  }


}