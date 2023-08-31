<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.page",
 *   hook = "block"
 * )
 */
class Page extends PreprocessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array {
    // Do any preprocessing here for your block!
    $handler = \Drupal::service('theme_handler');

    $variables['bank_name'] = theme_get_setting('bank_name');
    //$variables['active_domain'] = \Drupal::service('domain.negotiator')->getActiveId() ?: "htlf";
    $variables['active_domain'] = $active_domain = \Drupal::service('domain.negotiator')->getActiveId() != "pimentel_sandbox_lndo_site" ?: "htlf";
    $parent_theme_path = \Drupal::theme()->getActiveTheme()->getPath();
    $variables['banner_image'] = '/' . $parent_theme_path . '/domains/' . $active_domain . '/images/internal-banner-image.jpg';
    return $variables;
  }

}
