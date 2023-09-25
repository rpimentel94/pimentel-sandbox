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
 *   id = "talwind.paragraph__featured_product",
 *   hook = "block"
 * )
 */
class FeaturedProduct extends PreprocessPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function preprocess(array $variables): array
    {
        // Do any preprocessing here for your block!
        $paragraph = $variables['paragraph'];

        $variables['alignment'] = TailwindHelper::getAlignment($paragraph->get('field_alignment')->getString());
        //Add innter alignment to items
        if ($variables['alignment'] == 'justify-end') {
            $variables['alignment'] .= " text-right";
        } elseif ($variables['alignment'] == 'justify-center') {
            $variables['alignment'] .= " text-center";
        }
        $variables['body'] = [
            '#type' => 'processed_text',
            '#text' => $paragraph->get('field_textarea')->value,
            '#format' => $paragraph->get('field_textarea')->format,
        ];

        //Create Image
        if ($paragraph->hasField('field_image') && !$paragraph->get('field_image')->isEmpty()) {
            $media_field = $paragraph->get('field_image')->getString();
            $media_entity_load = Media::load($media_field);
            $style = ImageStyle::load('banner');
            $uri = $media_entity_load->field_media_image->entity->getFileUri();
            $variables['background_image'] = $style->buildUrl($uri);
        }

        //Create Button
        if ($paragraph->hasField('field_button') && !$paragraph->get('field_button')->isEmpty()) {
            $variables['button']['title'] = $paragraph->get('field_button')->first()->title ?: "";
            $variables['button']['url'] = TailwindHelper::createUrl($variables['button']['title'], $paragraph->get('field_button')->first()->getUrl()->toString(), $paragraph->get('field_button')->first()->getUrl());
            $variables['button']['color'] = TailwindHelper::getButtonColor('');
            $variables['button']['aria'] = $paragraph->get('field_button_aria_label')->getString() ?: "";
        }

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
        }

        return $variables;
    }

}