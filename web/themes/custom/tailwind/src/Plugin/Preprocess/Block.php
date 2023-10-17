<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\preprocess\PreprocessPluginBase;
use Drupal\tailwind\Plugin\Preprocess\TailwindHelper;
use Drupal\media\Entity\Media;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Url;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.block",
 *   hook = "block"
 * )
 */
class Block extends PreprocessPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function preprocess(array $variables): array
    {

        if ($variables['base_plugin_id'] == "system_menu_block") {

            $loader = \Drupal::service('domain.negotiator');
            $current_domain = $loader->getActiveDomain();
            $active_domain = $current_domain->id();
            $olb_settings = 'htlf_online_banking.' . $active_domain . '.settings';

            $olb_settings = \Drupal::config($olb_settings);

            $variables['online_banking'] = $olb_settings->get('enable_logins') ? TRUE : FALSE;

            if ($variables['online_banking']) {
                $variables['logins'] = [];

                $online_banking = [];
                $online_banking['name'] = "Online Banking";
                $online_banking['active'] = TRUE;
                $online_banking['url'] = theme_get_setting('login_url') ?: NULL;
                $online_banking['type'] = 'form';
                $online_banking['enroll'] = theme_get_setting('enroll_url') ?: NULL;
                $online_banking['recovery'] = theme_get_setting('recovery_url') ?: NULL;

                $variables['logins'][] = $online_banking;

                foreach ($olb_settings->get('login_settings') as $value) {
                    $variables['logins'][] = $value;
                }
            }
        }


        return $variables;
    }

}