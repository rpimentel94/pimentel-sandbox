<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__testimonial",
 *   hook = "block"
 * )
 */
class Testimonial extends PreprocessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];

    if ($paragraph->hasField('field_background_color')) {
      $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "htlfLightGray";
    }

    if ($paragraph->hasField('field_gutter')) {
      $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
    }

    $gutter = "";

    switch ($field_gutter) {
      case "none":
        $gutter = "";
        break;
      case "top":
        $gutter = "pt-24 sm:pt-12";
        break;
      case "bottom":
        $gutter = "pb-24 sm:pb-12";
        break;
        case "both":
          $gutter = "py-24 sm:py-12";
          break;
      default:
        $gutter = "";
    }

    $variables['gutter_option'] = $gutter;

    //$variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "htlfLightGray";


    return $variables;
  }

}
