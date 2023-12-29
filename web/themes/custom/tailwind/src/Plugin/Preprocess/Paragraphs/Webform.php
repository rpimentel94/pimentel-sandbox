<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\preprocess\PreprocessPluginBase;
use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;


/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__webform",
 *   hook = "block"
 * )
 */
class Webform extends PreprocessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array
  {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];

    $parent_field = str_replace('_q2_', '_', $paragraph->parent_field_name->getString());
    $variables['paragraph_width'] = str_contains($parent_field, "middle_section") ? "w-full m-auto" : "w-11/12 m-auto max-w-7xl";

    if ($paragraph->hasField('field_background_color')) {
      $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
      $variables['background_style'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "";
    }

    $variables['form_script_url'] = !$paragraph->get('field_text')->isEmpty() ? $paragraph->get('field_text')->getString() : "";

    $variables['show_sidebar'] = !$paragraph->get('field_display_section_sidebar')->isEmpty() ? $paragraph->get('field_display_section_sidebar')->getString() : "";

    $variables['sidebar_content'] = [
      '#type' => 'processed_text',
      '#text' => $paragraph->get('field_textarea')->value,
      '#format' => $paragraph->get('field_textarea')->format,
    ];

    $icon = $paragraph->get('field_fa_icon')->first();
    $variables['icon'] = [];
    $variables['icon']['classes'] = "fas fa-" . $icon->icon_name;


    //Gutters
    if ($paragraph->hasField('field_gutter')) {
      $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
      $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
    }

    return $variables;
  }

}