<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\preprocess\PreprocessPluginBase;
use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__call_to_action",
 *   hook = "block"
 * )
 */
class Cta extends PreprocessPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function preprocess(array $variables): array
    {
        // Do any preprocessing here for your block!
        $paragraph = $variables['paragraph'];
        $helper = new TailwindHelper;

        //Background Image
        if ($paragraph->hasField('field_background')) {
            $variables['background_type'] = !$paragraph->get('field_background')->isEmpty() ? $paragraph->get('field_background')->getString() : "htlfWhite";

            if ($variables['background_type'] == "image") {
                $media_field = $paragraph->get('field_image_secondary')->getString();
                $media_entity_load = Media::load($media_field);
                $style = ImageStyle::load('banner');
                $uri = $media_entity_load->field_media_image->entity->getFileUri();
                $variables['background_image'] = $style->buildUrl($uri);
                $variables['background_height'] = 'min-h-[28rem]';
            }
        }

        //Create Button
        $variables['button'] = [];
        $variables['button']['url'] = $paragraph->get('field_button')->first()->getUrl()->toString() != "" ? Url::fromUri($paragraph->get('field_button')->first()->getUrl()->toString()) : "#";
        $variables['button']['title'] = $paragraph->get('field_button')->first()->title ?: "";


        //Alignment
        if ($paragraph->hasField('field_alignment')) {
            $field_alignment = !$paragraph->get('field_alignment')->isEmpty() ? $paragraph->get('field_alignment')->getString() : "";
            $variables['alignment'] = $helper->getAlignment($field_alignment);
        }

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = $helper->getGutter($field_gutter);
        }

        return $variables;
    }

}