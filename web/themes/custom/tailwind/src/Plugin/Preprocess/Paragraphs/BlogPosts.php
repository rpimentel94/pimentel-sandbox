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
 *   id = "talwind.paragraph__blog_posts",
 *   hook = "block"
 * )
 */
class BlogPosts extends PreprocessPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function preprocess(array $variables): array
    {
        // Do any preprocessing here for your block!
        $paragraph = $variables['paragraph'];
        
        if ($paragraph->hasField('field_background_color')) {
            $background_color = $paragraph->get('field_background_color')->getString() ?: "htlfWhite";
            $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfWhite";
            if ($background_color == "primary" || $background_color == "secondary") {
                $variables['text_color'] = "text-htlfWhite";
            }
        }

        $blog_tags = [];
        //Blog Tags
        if ($paragraph->hasField('field_blog_tag_filter')) {
            $tags = $paragraph->field_blog_tag_filter->referencedEntities();

            foreach ($tags as $key => $value) {
                echo $value->get('tid')->getString();
            }
        }

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
        }

        return $variables;
    }

}