<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\preprocess\PreprocessPluginBase;
use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.page",
 *   hook = "block"
 * )
 */
class Page extends PreprocessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array
  {
    // Do any preprocessing here for your block!
    $handler = \Drupal::service('theme_handler');

    $variables['bank_name'] = theme_get_setting('bank_name');
    $variables['active_domain'] = $active_domain = \Drupal::service('domain.negotiator')->getActiveId() != "pimentel_sandbox_lndo_site" ?: "htlf";
    $parent_theme_path = \Drupal::theme()->getActiveTheme()->getPath();

    $variables['page_type'] = NULL;

    if (array_key_exists('node', $variables)) {

      $variables['page_type'] = $variables['node']->getType();

      //Create Image
      if ($variables['node']->hasField('field_internal_banner_image') && !$variables['node']->get('field_internal_banner_image')->isEmpty()) {
        $media_field = $variables['node']->get('field_internal_banner_image')->getString();
        $media_entity_load = Media::load($media_field);
        $style = ImageStyle::load('banner');
        $uri = $media_entity_load->field_media_image->entity->getFileUri();
        $variables['banner_image'] = $style->buildUrl($uri);
      } else {
        $variables['banner_image'] = '/' . $parent_theme_path . '/domains/' . $active_domain . '/images/internal-banner-image.jpg';
      }

      //Check for sidebar
      if ($variables['node']->hasField('field_sidebar_enabled') && !$variables['node']->get('field_sidebar_enabled')->isEmpty()) {
        $variables['sidebar'] = $variables['node']->get('field_sidebar_enabled')->getString() == 1 ? TRUE : FALSE;
      }

      //Check for sidebar
      $variables['middle_section'] = $variables['node']->hasField('field_middle_section') && !$variables['node']->get('field_middle_section')->isEmpty() ? TRUE : FALSE;
    }


    return $variables;
  }

}