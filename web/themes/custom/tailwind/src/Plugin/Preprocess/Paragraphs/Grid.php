<?php

namespace Drupal\tailwind\Plugin\Preprocess\Paragraphs;

use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\preprocess\PreprocessPluginBase;
use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\fontawesome\FontAwesomeManagerInterface;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.paragraph__grid",
 *   hook = "block"
 * )
 */
class Grid extends PreprocessPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function preprocess(array $variables): array
    {
        // Do any preprocessing here for your block!
        $paragraph = $variables['paragraph'];

        $variables['theme_color'] = theme_get_setting('primary_color');

        if ($paragraph->hasField('field_background_color')) {
            $background_color = $paragraph->get('field_background_color')->getString() ?: "htlfBody";
            $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
            if ($background_color == "primary" || $background_color == "secondary") {
                $variables['text_color'] = "text-htlfWhite";
            }
        }

        //Grid Style 
        if ($paragraph->hasField('field_grid_style')) {
            $variables['grid_style'] = $paragraph->get('field_grid_style')->getString();
        }

        //Grid Columns
        if ($paragraph->hasField('field_num_cols')) {
            $columns = $paragraph->get('field_num_cols')->getString();
            switch ($columns) {
                case 'two':
                    $variables['columns'] = "w-full md:max-w-[49%]";
                    break;
                case 'three':
                    $variables['columns'] = "md:max-w-[32%]";
                    break;
                case 'four':
                    $variables['columns'] = "md:max-w-[23%]";
                    break;
            }
        }

        $grid_items = [];
        //Grid Items
        if ($paragraph->hasField('field_grid_item')) {
            $grids = $paragraph->field_grid_item->referencedEntities();

            foreach ($grids as $grid) {
                //Create empty item to append altered data to
                $item = [];

                $item['title'] = $grid->get('field_text')->getString();
                //$item['body'] = $grid->get('field_textarea')->getString();
                $item['body'] = [
                    '#type' => 'processed_text',
                    '#text' => $grid->get('field_textarea')->value,
                    '#format' => $grid->get('field_textarea')->format,
                ];

                //Image Type
                if (!$grid->get('field_icon_type')->isEmpty()) {
                    if ($grid->get('field_icon_type')->getString() == 'fa') {
                        $item['image_type'] = "fa";
                        //Consider rendering out FA Icon regularly
                        $icon = $grid->get('field_fa_icon')->first();
                        $item['fa'] = [];
                        $item['fa']['classes'] = $icon->style . " fa-" . $icon->icon_name;
                        //dd($item['fa']);
                    } elseif ($grid->get('field_icon_type')->getString() == 'img') {
                        $item['image_type'] = "image";
                        $media_field = $grid->get('field_image')->getString();
                        $media_entity_load = Media::load($media_field);
                        $style = ImageStyle::load('large');
                        $uri = $media_entity_load->field_media_image->entity->getFileUri();
                        $item['image'] = $style->buildUrl($uri);
                    }
                }

                //Create Button
                $item['button']['url'] = $grid->get('field_button')->first()->getUrl()->toString() != "" ? Url::fromUri($grid->get('field_button')->first()->getUrl()->toString()) : "#";
                $item['button']['title'] = $grid->get('field_button')->first()->title ?: "";
                $item['button']['color'] = TailwindHelper::getButtonColor('');
                $item['button']['aria'] = $grid->get('field_button_aria_label')->getString() ?: "";
                $grid_items[] = $item;
            }

            $variables['grid_items'] = $grid_items;
        }

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
        }

        return $variables;
    }

}