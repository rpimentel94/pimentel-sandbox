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
    //$variables['bank_name'] = \Drupal::service('domain.current_domain_context')->getHostname();
    $negotiator = \Drupal::service('domain.negotiator');
    $variables['active_domain'] = $active_domain = $negotiator->getActiveId();
    $variables['bank_name'] = $negotiator->getActiveDomain()->toString();

    $corporate_domains = ['rps', 'pwm', 'htlf', 'pimentel_sandbox_lndo_site'];
    $variables['main_menu'] = !in_array($active_domain, $corporate_domains) ? "main" : "main-" . $active_domain;
    $parent_theme_path = \Drupal::theme()->getActiveTheme()->getPath();

    //dd(\Drupal::service('domain.negotiator')->getActiveId());

    $variables['page_type'] = NULL;

    if (array_key_exists('node', $variables)) {

      $variables['page_type'] = $variables['node']->getType();

      $variables['title'] = $variables['node']->label();

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
    
    } elseif (array_key_exists('#title', $variables['page']) && $variables['page']['#title'] == "Blog Posts") {
      $variables['banner_image'] = '/' . $parent_theme_path . '/domains/' . $active_domain . '/images/internal-banner-image.jpg';
    }

    //$active_trail = \Drupal::service('menu.active_trail');

    //Replace `main` with the name of the menu you're working with.
    //$menu_level = count($active_trail->getActiveTrailIds('main-htlf'));

    // $loader = \Drupal::service('domain.negotiator');
    // $current_domain = $loader->getActiveDomain();
    // $active_domain = $current_domain->id();
    // $olb_settings = 'htlf_online_banking.' . $active_domain . '.settings';

    // $olb_settings = \Drupal::config($olb_settings);

    // $variables['online_banking'] = $olb_settings->get('enable_logins') ? TRUE : FALSE;

    // if ($variables['online_banking']) {
    //   $variables['logins'] = [];
    //   foreach ($olb_settings->get('login_settings') as $value) {
    //     $variables['logins'][] = $value;
    //   }
    // }


    return $variables;
  }

}