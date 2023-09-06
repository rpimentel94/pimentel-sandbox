<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__uneven_columns",
 *   hook = "block"
 * )
 */
class UnevenColumns extends PreprocessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array
  {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];

    if ($paragraph->hasField('field_background_color')) {
      $background_color = $paragraph->get('field_background_color')->getString() ?: "htlfBody";
      $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
      if ($background_color == "primary" || $background_color == "secondary") {
        $variables['text_color'] = "text-htlfWhite";
      }
    }

    //Gutters
    if ($paragraph->hasField('field_gutter')) {
      $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
      $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
    }

    if ($paragraph->hasField('field_col_split')) {
        $splits = [];
        switch ($paragraph->get('field_col_split')->getString()) {
            case '25-75':
                $splits['left'] = 'basis-3/12';
                $splits['right'] = 'basis-9/12';
                break;
            case '75-25':
                $splits['left'] = 'basis-9/12';
                $splits['right'] = 'basis-3/12';
                break;
            case '33-66':
                $splits['left'] = 'basis-4/12';
                $splits['right'] = 'basis-8/12';
                break;
            case '66-33':
                $splits['left'] = 'basis-8/12';
                $splits['right'] = 'basis-4/12';
                break;
            case '40-60':
                $splits['left'] = 'basis-2/5';
                $splits['right'] = 'basis-3/5';
                break;
            case '60-40':
                $splits['left'] = 'basis-3/5';
                $splits['right'] = 'basis-2/5';
                break;
            default:
                $splits['left'] = 'basis-2/5';
                $splits['right'] = 'basis-3/5';
                break;
        }

        $variables['splits'] = $splits;
    }

    $variables['button'] = [];

    //Create Button
    if (!$paragraph->get('field_button')->isEmpty()) {
    $variables['button']['first']['url'] = $paragraph->get('field_button')->first()->getUrl()->toString() != "" ? Url::fromUri($paragraph->get('field_button')->first()->getUrl()->toString()) : "#";
    $variables['button']['first']['title'] = $paragraph->get('field_button')->first()->title ?: "";
    $variables['button']['first']['color'] = TailwindHelper::getButtonColor($background_color);
    }

    //Create Second Button
    if (!$paragraph->get('field_button_secondary')->isEmpty()) {
    $variables['button']['second']['url'] = $paragraph->get('field_button_secondary')->first()->getUrl()->toString() != "" ? Url::fromUri($paragraph->get('field_button')->first()->getUrl()->toString()) : "#";
    $variables['button']['second']['title'] = $paragraph->get('field_button_secondary')->first()->title ?: "";
    $variables['button']['second']['color'] = TailwindHelper::getButtonColor($background_color);
    }

    return $variables;
  }

}