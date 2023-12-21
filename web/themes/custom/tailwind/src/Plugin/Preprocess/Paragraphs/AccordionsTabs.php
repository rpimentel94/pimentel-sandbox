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
 *   id = "talwind.paragraph__accordions_tabs",
 *   hook = "block"
 * )
 */
class AccordionsTabs extends PreprocessPluginBase
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
            $variables['background_color'] = !$paragraph->get('field_background_color')->isEmpty() ? TailwindHelper::getColor($paragraph->get('field_background_color')->getString()) : "htlfBody";
            $variables['background_style'] = !$paragraph->get('field_background_color')->isEmpty() ? $paragraph->get('field_background_color')->getString() : "";
        }

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
        }

        if ($paragraph->hasField('field_widget_type')) {
            $variables['widget_style'] = !$paragraph->get('field_widget_type')->isEmpty() ? $paragraph->get('field_widget_type')->getString() : "tabs";
        }

        // Create Empty Tabs
        $tab_items = [];

        if ($paragraph->hasField('field_accordion_tab_item')) {
            $tabs = $paragraph->field_accordion_tab_item->referencedEntities();

            foreach ($tabs as $tab) {
                //Create empty item to append altered data to
                $item = [];

                if ($tab->getType() == "accordion_tab_item") {
                    $item['title'] = $tab->get('field_text')->getString();
                    $item['link_title'] = strtolower(str_replace(" ", "-", $item['title']));
                    $item['tag'] = $tab->get('field_tag')->getString();
                    $item['type'] = "accordion_item";

                    $item['body'] = [
                        '#type' => 'processed_text',
                        '#text' => $tab->get('field_textarea')->value,
                        '#format' => $tab->get('field_textarea')->format,
                    ];

                    //Create Button
                    if ($tab->hasField('field_button') && !$tab->get('field_button')->isEmpty()) {
                        $item['button']['title'] = $tab->get('field_button')->first()->title ?: "";
                        $item['button']['url'] = TailwindHelper::createUrl($item['button']['title'], $tab->get('field_button')->first()->getUrl()->toString(), $tab->get('field_button')->first()->getUrl());
                        $item['button']['color'] = TailwindHelper::getButtonColor('');
                        $item['button']['aria'] = $tab->get('field_button_aria_label')->getString() ?: "";
                    }
                } else {

                    $item['title'] = $tab->get('field_admin_title')->getString() ?: "";
                    $item['type'] = "team_members";
                    $item['tag'] = "h2";
                    $item['link_title'] = strtolower(str_replace(" ", "-", $item['title']));
                    $item['content'] = $tab;


                }
                $tab_items[] = $item;
            }

            $variables['tabs'] = $tab_items;
        }
        return $variables;
    }

}