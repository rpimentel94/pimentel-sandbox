<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\preprocess\PreprocessPluginBase;
use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;

/**
 * Custom Node Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.node",
 *   hook = "block"
 * )
 */
class Node extends PreprocessPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function preprocess(array $variables): array
    {
        // Do any preprocessing here for your block!
        $handler = \Drupal::service('theme_handler');

        $node = $variables['node'];

        //Check for sidebar
        if ($node->hasField('field_sidebar_enabled') && !$node->get('field_sidebar_enabled')->isEmpty()) {
            $variables['sidebar'] = $node->get('field_sidebar_enabled')->getString() == 1 ? TRUE : FALSE;
        }

        $variables['middle_section'] = FALSE;

        //Check for sidebar
        if ($node->hasField('field_middle_section') && !$node->get('field_middle_section')->isEmpty() && !$node->get('field_middle_section')->first()->isEmpty()) {
            $paragraphs = $node->get('field_middle_section')->referencedEntities();

            foreach ($paragraphs as $paragraph) {
                if (isset($paragraph)) {
                    $variables['middle_section'] = TRUE;
                }
            }
        }


        return $variables;
    }

}