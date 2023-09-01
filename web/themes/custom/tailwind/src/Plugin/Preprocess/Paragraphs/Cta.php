<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;
use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;

use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__call_to_action",
 *   hook = "block"
 * )
 */
class Cta extends PreprocessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array {
    // Do any preprocessing here for your block!
    $paragraph = $variables['paragraph'];

    if ($paragraph->hasField('field_background')) {
        $variables['background_type'] = !$paragraph->get('field_background')->isEmpty() ? $paragraph->get('field_background')->getString() : "htlfWhite";

        if ($variables['background_type'] == "image") {
            $media_field = $paragraph->get('field_image_secondary')->getString();
            $media_entity_load = Media::load($media_field);
            $style = ImageStyle::load('banner');
            $uri = $media_entity_load->field_media_image->entity->getFileUri();
            $variables['background_image'] = $style->buildUrl($uri);
        }
      }

    return $variables;
  }

}
