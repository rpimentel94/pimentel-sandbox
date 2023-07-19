<?php

namespace Drupal\tailwind\Plugin\Preprocess;

use Drupal\preprocess\PreprocessPluginBase;

/**
 * Custom Page Preprocessor.
 *
 * @Preprocess(
 *   id = "talwind.page",
 *   hook = "block"
 * )
 */
class Page extends PreprocessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function preprocess(array $variables): array {
    // Do any preprocessing here for your block!

    $variables['bank_name'] = theme_get_setting('bank_name');
    return $variables;
  }

}
