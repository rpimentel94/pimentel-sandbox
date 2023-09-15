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

        //Grid Style & determine background
        if ($paragraph->hasField('field_grid_style')) {
            $variables['grid_style'] = $paragraph->get('field_grid_style')->getString();
            switch ($paragraph->get('field_grid_style')->getString()) {
                case 'cards':
                    $variables['item_background'] = "bg-htlfWhite shadow-2xl drop-shadow-2xl";
                    break;
                case 'callouts':
                    $variables['item_background'] = "bg-htlfWhite";
                    break;
                case 'smallcallouts':
                    $variables['item_background'] = "bg-htlfWhite shadow-2xl drop-shadow-2xl";
                    break;
                default:
                    $variables['item_background'] = "";
                    break;
            }
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
            $variables['col_count'] = $columns == "four" ? " justify-start gap-x-8 " : " justify-between ";

            //Check against Grid Style
            if ($variables['grid_style'] == "callouts") {
                $variables['col_count'] .= " shadow-2xl drop-shadow-2xl bg-htlfBody py-8 ";
                if ($columns == "three") {
                    $variables['columns'] = "md:max-w-[33%] pb-10";
                }
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
                        $uri = $media_entity_load ? $media_entity_load->field_media_image->entity->getFileUri() : "";
                        $item['image'] = $style->buildUrl($uri);
                    }
                }

                //Create Button
                if ($grid->hasField('field_button') && !$grid->get('field_button')->isEmpty()) {
                    $item['button']['title'] = $grid->get('field_button')->first()->title ?: "";
                    $item['button']['url'] = TailwindHelper::createUrl($item['button']['title'], $grid->get('field_button')->first()->getUrl()->toString(), $grid->get('field_button')->first()->getUrl());
                    $item['button']['color'] = TailwindHelper::getGridButtonColor('primary', $variables['grid_style']);
                    $item['button']['aria'] = $grid->get('field_button_aria_label')->getString() ?: "";
                    $grid_items[] = $item;
                }
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