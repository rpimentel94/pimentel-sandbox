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
 *   id = "talwind.paragraph__team_members",
 *   hook = "block"
 * )
 */
class TeamMembers extends PreprocessPluginBase
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

        $views_filter = "";

        //Blog Tags
        if ($paragraph->hasField('field_team_member_group') && !$paragraph->get('field_team_member_group')->isEmpty()) {
            $groups = $paragraph->field_team_member_group->referencedEntities();
            foreach ($groups as $key => $value) {
                $views_filter = $value->get('tid')->getString();
            }
        }

        $view = Views::getView('team_members');
        $view->setDisplay('team_members');
        //contextual relationship filter
        $view->setArguments([$views_filter]);
        $view->execute();
        $rendered = $view->render();
        $variables['team_members'] = \Drupal::service('renderer')->render($rendered);

        //Gutters
        if ($paragraph->hasField('field_gutter')) {
            $field_gutter = !$paragraph->get('field_gutter')->isEmpty() ? $paragraph->get('field_gutter')->getString() : "";
            $variables['gutter_option'] = TailwindHelper::getGutter($field_gutter);
        }

        return $variables;
    }

}