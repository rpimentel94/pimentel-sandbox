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

    $parent_field = str_replace('_q2_', '_', $paragraph->parent_field_name->getString());
    $variables['paragraph_width'] = str_contains($parent_field, "middle_section") ? "w-full m-auto" : "w-11/12 xl:w-8/12 m-auto max-w-7xl";

    if ($paragraph->hasField('field_background_color')) {
      $background_color = $paragraph->get('field_background_color')->getString() ?: "htlfBody";
      $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
      if ($background_color == "primary" || $background_color == "secondary") {
        $variables['text_color'] = "text-htlfWhite";
      }
      $variables['background_style'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "";
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
                $splits['left'] = 'md:basis-3/12';
                $splits['right'] = 'md:basis-9/12';
                break;
            case '75-25':
                $splits['left'] = 'md:basis-9/12';
                $splits['right'] = 'md:basis-3/12';
                break;
            case '33-66':
                $splits['left'] = 'md:basis-4/12';
                $splits['right'] = 'md:basis-8/12';
                break;
            case '66-33':
                $splits['left'] = 'md:basis-8/12';
                $splits['right'] = 'md:basis-4/12';
                break;
            case '40-60':
                $splits['left'] = 'md:basis-2/5';
                $splits['right'] = 'md:basis-3/5';
                break;
            case '60-40':
                $splits['left'] = 'md:basis-3/5';
                $splits['right'] = 'md:basis-2/5';
                break;
            default:
                $splits['left'] = 'md:basis-2/5';
                $splits['right'] = 'md:basis-3/5';
                break;
        }

        $variables['splits'] = $splits;
    }

    $variables['button'] = [];

    //Create Button
    if (!$paragraph->get('field_button')->isEmpty()) {
    $variables['button']['first']['title'] = $paragraph->get('field_button')->first()->title ?: "";
    $variables['button']['first']['url'] = TailwindHelper::createUrl($variables['button']['first']['title'], $paragraph->get('field_button')->first()->getUrl()->toString(), $paragraph->get('field_button')->first()->getUrl());
    $variables['button']['first']['color'] = TailwindHelper::getButtonColor($background_color);
    }

    //Create Second Button
    if (!$paragraph->get('field_button_secondary')->isEmpty()) {
    $variables['button']['second']['title'] = $paragraph->get('field_button_secondary')->first()->title ?: "";
    $variables['button']['second']['url'] = TailwindHelper::createUrl($variables['button']['second']['title'], $paragraph->get('field_button_secondary')->first()->getUrl()->toString(), $paragraph->get('field_button_secondary')->first()->getUrl());
    $variables['button']['second']['color'] = TailwindHelper::getButtonColor($background_color);
    }

    return $variables;
  }

}