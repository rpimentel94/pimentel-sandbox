<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__rates",
 *   hook = "block"
 * )
 */
class Rates extends PreprocessPluginBase
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

    $rate_items = [];
        //Rate Items
        if ($paragraph->hasField('field_rate_item')) {
            $rates = $paragraph->field_rate_item->referencedEntities();

            foreach ($rates as $rate) {
                //Create empty item to append altered data to
                $item = [];

                $item['title'] = $rate->get('field_text')->getString();
                $item['qualifying_text'] = $rate->get('field_qualifying_text')->getString() ?: "as low as";
                $item['rate_symbol'] = $rate->get('field_rate_symbol')->getString();
                $item['rate_value'] = $rate->get('field_rate_value')->getString();
                $item['rate_type'] = $rate->get('field_rate_type')->getString();
                

                //Create Button
                if ($rate->hasField('field_rate_link') && !$rate->get('field_rate_link')->isEmpty()) {
                    $item['button']['title'] = $rate->get('field_text')->getString();
                    $item['button']['url'] = TailwindHelper::createUrl($item['button']['title'], $rate->get('field_rate_link')->first()->getUrl()->toString(), $rate->get('field_rate_link')->first()->getUrl());
                    $item['button']['color'] = TailwindHelper::getColor('primary');
                }
                $rate_items[] = $item;
            }

            $variables['rate_items'] = $rate_items;
        }

    return $variables;
  }

}