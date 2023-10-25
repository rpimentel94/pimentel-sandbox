<?php

namespace Drupal\htlf_tokens\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\config_pages\Entity\ConfigPages;
use Drupal\media\Entity\Media;

/**
 * Filter that replaces custom token values in the WYSWIYG.
 *
 * @Filter(
 *   title = @Translation("Custom Token Replace Filter"),
 *   id = "custom_token_sheet_filter",
 *   description = @Translation("Replace custom tokens with token sheet data/config."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE
 * )
 */
class CustomTokenSheetFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    
    $loader = \Drupal::service('domain.negotiator');
    $current_domain = $loader->getActiveDomain();
    $active_domain = $current_domain->id();
    $token_sheet = 'htlf_tokens.' . $active_domain . '.settings';
    
    $token_sheet = \Drupal::config($token_sheet);

    $token_sheet_data = unserialize($token_sheet->get('token_sheet_data'));

    $tokens = [];
    $token_values = [];

    foreach ($token_sheet_data as $values) {
        foreach ($values as $key => $value) {
            $tokens[] = $value['token_name'];
            $token_values[] = $value['value'];
        }
    }


    $new_text = str_replace($tokens, $token_values, $text);
    $response = new FilterProcessResult($new_text);
    $response->setProcessedText($new_text);
    return $response->addCacheTags(['token_list']);
  }

}
