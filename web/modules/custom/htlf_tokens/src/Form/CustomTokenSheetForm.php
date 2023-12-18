<?php

namespace Drupal\htlf_tokens\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;

/**
 * Configure htlf_tokens settings for this site.
 */
class CustomTokenSheetForm extends ConfigFormBase
{

    /** 
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'htlf_tokens.settings';

    /**
     * Get the current domain specific settings
     */
    public function getDomainSettings()
    {
        $loader = \Drupal::service('domain.negotiator');
        $current_domain = $loader->getActiveDomain();
        $active_domain = $current_domain->id();
        $config_name = 'htlf_tokens.' . $active_domain . '.settings';
        return $config_name;

    }

    /** 
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'htlf_tokens_admin_settings';
    }

    /** 
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            $this->getDomainSettings(),
        ];
    }

    /** 
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config($this->getDomainSettings());

        $form['token_sheet'] = [
            '#type' => 'media_library',
            '#allowed_bundles' => ['csv', 'image'],
            '#title' => t('Upload your csv'),
            '#default_value' => $config->get('token_sheet') ?: "",
            '#description' => t('Upload updated custom token sheet'),
        ];

        return parent::buildForm($form, $form_state);
    }

    /** 
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

        $config_array = serialize($this->createConfigArray($form_state->getValue('token_sheet')));

        $this->config($this->getDomainSettings())
            // Set the submitted configuration setting.
            ->set('token_sheet_id', $form_state->getValue('token_sheet'))
            ->set('token_sheet_data', $config_array)
            ->save();

        parent::submitForm($form, $form_state);
    }

    public function createConfigArray($mid)
    {
        $media = Media::load($mid);
        $fid = $media->field_media_file_csv->target_id;
        $file = File::load($fid);

        $tokens = $sorted_data = [];

        if ($file) {
            //loop through the csv file into an array
            $tokens = array_map('str_getcsv', file($file->getFileUri(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            /*Walk through the array and combine the headers which is the first element of our csv array with the rest of the csv data*/
            array_walk($tokens, function (&$ary) use ($tokens) {
                $ary = array_combine($tokens[0], $ary);
            });

            //remove column headers which is the first element
            array_shift($tokens);
            foreach ($tokens as $key => $value) {
                $tokens[$key]['Type'] = strtolower($value['Type']);
            }

            uasort($tokens, function ($a, $b) {
                return strcmp($a['Type'], $b['Type']);
            });


            foreach ($tokens as $key => $value) {
                if (!array_key_exists($value['Type'], $sorted_data)) {
                    $sorted_data[$value['Type']] = [];
                }

                $sorted_data[$value['Type']][$value['Token Name']] = [
                    'name' => $value['Name'],
                    'dynamic' => FALSE,
                    'description' => $value['Name'],
                    'token_name' => "[" . $value['Type'] . ":" . $value['Token Name'] . "]",
                    'value' => $value['Value'],
                ];
            }
        }

        return $sorted_data;
    }

}