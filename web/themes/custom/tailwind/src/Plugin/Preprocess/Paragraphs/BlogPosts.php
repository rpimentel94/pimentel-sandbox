<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\preprocess\PreprocessPluginBase;
use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\views\Views;

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

        $views_filter = [];

        $blog_tags = "";
        //Blog Tags
        if ($paragraph->hasField('field_blog_tag_filter') && !$paragraph->get('field_blog_tag_filter')->isEmpty()) {
            $tags = $paragraph->field_blog_tag_filter->referencedEntities();

            foreach ($tags as $key => $value) {
                $blog_tags .= $value->get('tid')->getString() . ",";
            }
            $views_filter[] = substr_replace($blog_tags ,"", -1);
        }

        

        $blog_categories = "";
        //Blog Categories
        if ($paragraph->hasField('field_blog_category_filter') && !$paragraph->get('field_blog_category_filter')->isEmpty()) {
            $categories = $paragraph->field_blog_category_filter->referencedEntities();

            foreach ($categories as $key => $value) {
                $blog_categories .= $value->get('tid')->getString() . ",";
            }
            $views_filter[] = substr_replace($blog_categories ,"", -1);
        }

        $display_type = NULL;
        //Display Type
        if ($paragraph->hasField('field_display_type')) {
            $custom_views = ['topics', 'personalize'];
            $display = $paragraph->field_display_type->getString();
            $display_type =  !in_array($paragraph->field_display_type->getString(), $custom_views) ? $paragraph->field_display_type->getString() : "recent" ;
        }

        if ($display_type) {
            $view = Views::getView('blog_posts');
            $view->setDisplay($display_type);
            // contextual relationship filter
            $view->setArguments($views_filter);
            $view->execute();
            $rendered = $view->render();
            $variables['blogs'] = \Drupal::service('renderer')->render($rendered);
        }

        if ($display == "topics") {
            
        }

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
        }

        return $variables;
    }

}