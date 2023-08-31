<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom MainMenu Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.main_menu",
 *   hook = "block"
 * )
 */
class MainMenu extends PreprocessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array {
    // Do any preprocessing here for your block!
    return $variables;
  }

}
