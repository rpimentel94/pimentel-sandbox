<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__testimonial",
 *   hook = "block"
 * )
 */
class Testimonial extends PreprocessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array
  {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];
    
    if ($paragraph->hasField('field_background_color')) {
      $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
      $variables['background_style'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "";
    }

    //Gutters
    if ($paragraph->hasField('field_gutter')) {
      $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
      $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
    }

    return $variables;
  }

}