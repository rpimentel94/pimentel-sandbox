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
 *   id = "talwind.paragraph__banner",
 *   hook = "block"
 * )
 */
class Banner extends PreprocessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array
  {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];
    $parent = $paragraph->getParentEntity();

    $variables['slide_height'] = $parent->getType() == 'landing_page' ? "min-h-[31rem]" : "min-h-[24rem]";

    // Create Empty slides
    $slide_items = [];
    
    if ($paragraph->hasField('field_banner_item')) {
      $slides = $paragraph->field_banner_item->referencedEntities();

      foreach ($slides as $slide) {
        //Create empty item to append altered data to
        $item = [];
        $item['alignment'] = TailwindHelper::getAlignment($slide->get('field_alignment')->getString());
            //Add innter alignment to items
            if ($item['alignment'] == 'justify-end') {
                $item['alignment'] .= " text-right";
            } elseif ($item['alignment'] == 'justify-center') {
                $item['alignment'] .= " text-center";
            }
        $item['overlay'] = $slide->get('field_overlay')->getString() == "dark" ? "bg-slate-900 bg-opacity-25" : "";
        $item['title'] = $slide->get('field_text')->getString(); 
        $item['body'] = [
          '#type' => 'processed_text',
          '#text' => $slide->get('field_textarea_plain')->value,
          '#format' => $slide->get('field_textarea_plain')->format,
      ];
        

        //Create Image
        if ($slide->hasField('field_image') && !$slide->get('field_image')->isEmpty()) {
            $media_field = $slide->get('field_image')->getString();
            $media_entity_load = Media::load($media_field);
            $style = ImageStyle::load('banner');
            $uri = $media_entity_load->field_media_image->entity->getFileUri();
            $item['background_image'] = $style->buildUrl($uri);
        }

        //Create Button
        if ($slide->hasField('field_button') && !$slide->get('field_button')->isEmpty()) {
          $item['button']['title'] = $slide->get('field_button')->first()->title ?: "";
          $item['button']['url'] = TailwindHelper::createUrl($item['button']['title'], $slide->get('field_button')->first()->getUrl()->toString(), $slide->get('field_button')->first()->getUrl());
          $item['button']['color'] = TailwindHelper::getButtonColor('');
          $item['button']['aria'] = $slide->get('field_button_aria_label')->getString() ?: "";
        }
        $slide_items[] = $item;
      }

      $variables['slides'] = $slide_items;
    }

    return $variables;
  }

}