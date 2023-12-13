<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__general_content_section",
 *   hook = "block"
 * )
 */
class GeneralContent extends PreprocessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array
  {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];

    $parent_field = str_replace('_q2_', '_', $paragraph->parent_field_name->getString());
    $variables['paragraph_width'] =  $parent_field == "field_middle_section" ? "w-full m-auto" : "w-11/12 xl:w-8/12 m-auto max-w-7xl";
    
    if ($paragraph->hasField('field_background_color')) {
      $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
      $variables['background_style'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "";
    }

    $variables['body'] = [
        '#type' => 'processed_text',
        '#text' => $paragraph->get('field_textarea')->value,
        '#format' => $paragraph->get('field_textarea')->format,
    ];

    //Gutters
    if ($paragraph->hasField('field_gutter')) {
      $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
      $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
    }

    return $variables;
  }

}