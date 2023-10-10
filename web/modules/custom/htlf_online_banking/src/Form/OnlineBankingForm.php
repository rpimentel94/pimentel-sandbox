<?php

namespace Drupal\htlf_online_banking\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure htlf_online_banking settings for this site.
 */
class OnlineBankingForm extends ConfigFormBase
{

    /** 
     * Config settings.
     *
     * @var string
     */
    const SETTINGS = 'htlf_online_banking.settings';

    /** 
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'htlf_online_banking_admin_settings';
    }

    /** 
     * {@inheritdoc}
     */
    protected function getEditableConfigNames()
    {
        return [
            static::SETTINGS,
        ];
    }

    /** 
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $config = $this->config(static::SETTINGS);

        $form['default_title'] = array(
            '#type' => 'textfield',
            '#title' => t('Default Login Name'),
            '#required' => TRUE,
            '#default_value' => $config->get('default_title') ?: ""
        );

        $form['enable_logins'] = array(
            '#type' => 'checkbox',
            '#default_value' => $config->get('enable_logins') ?: FALSE,
            '#title' => t('Enable Online Banking Logins'),
            '#required' => FALSE,
        );


        $form['logins'] = [
            '#type' => 'details',
            '#title' => "Logins",
            '#open' => TRUE,
            '#description' => "Add & remove login options to appear within the login modal",
        ];

        $form['logins']['addLogin'] = [
            '#type' => 'submit',
            '#value' => t('Add Login Option'),
            '#submit' => ['::addOneLogin'],
            '#weight' => 100,
            '#ajax' => [
                'callback' => '::updateTagCallback',
                'wrapper' => 'login-fields-wrapper',
                'method' => 'replace',
            ],
        ];
        $form['logins']['deleteLogin'] = [
            '#type' => 'submit',
            '#value' => t('Remove Last Login Section'),
            '#submit' => ['::remOneLogin'],
            '#weight' => 100,
            '#ajax' => [
                'callback' => '::updateTagCallback',
                'wrapper' => 'login-fields-wrapper',
                'method' => 'replace',
            ],
        ];
        $form['logins']['login_values'] = [
            '#type' => 'container',
            '#tree' => TRUE,
            '#prefix' => '<div id="login-fields-wrapper">',
            '#suffix' => '</div>',
        ];
        $number_of_logins = $form_state->get('number_of_logins');

        $values = [
            'login' => t('Login'),
            'url' => t('URL')
        ];

        if (empty($number_of_logins)) {
            $number_of_logins = 1;
            $form_state->set('number_of_logins', $number_of_logins);
        }
        for ($i = 0; $i < $number_of_logins; $i++) {

            $form['logins']['login_values'][$i]['name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Name'),
                '#default_value' => "",
            ];

            $form['logins']['login_values'][$i]['active'] = array(
                '#type' => 'checkbox',
                '#default_value' => FALSE,
                '#title' => t('Active'),
                '#required' => FALSE,
            );

            $form['logins']['login_values'][$i]['type'] = array(
                '#title' => t('Login Type'),
                '#type' => 'select',
                '#options' => $values,
                '#default_value' => 'login'
              );

            $form['logins']['login_values'][$i]['url'] = [
                '#type' => 'textfield',
                '#title' => $this->t('URL'),
                '#default_value' => "",
            ];

            $form['logins']['login_values'][$i]['form_action'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Form Action'),
                '#default_value' => "",
            ];

            $form['logins']['login_values'][$i]['user_name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('User ID Name Override'),
                '#default_value' => "",
            ];

            $form['logins']['login_values'][$i]['pass_name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Password Name Override'),
                '#default_value' => "",
            ];
        }
        //return $form;

        return parent::buildForm($form, $form_state);
    }

    /** 
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        dd($form_state->getValues());
        // Retrieve the configuration.
        $this->config(static::SETTINGS)
            // Set the submitted configuration setting.
            ->set('example_thing', $form_state->getValue('example_thing'))
            // You can set multiple configurations at once by making
            // multiple calls to set().
            ->set('other_things', $form_state->getValue('other_things'))
            ->save();

        parent::submitForm($form, $form_state);
    }

    //Add Callback methods.
    /**
     * Add or Increment number of logins.
     */
    public function addOneLogin(array &$form, FormStateInterface $form_state)
    {
        $number_of_logins = $form_state->get('number_of_logins');
        $form_state->set('number_of_logins', $number_of_logins + 1);
        $form_state->setRebuild(TRUE);
    }
    /**
     * Remove or Decrement number of logins.
     */
    public function remOneLogin(array &$form, FormStateInterface $form_state)
    {
        $number_of_logins = $form_state->get('number_of_logins');
        $form_state->set('number_of_logins', $number_of_logins - 1);
        $form_state->setRebuild(TRUE);
    }
    /**
     * Return the tag list (Form).
     */
    public function updateTagCallback(array &$form, FormStateInterface $form_state)
    {
        return $form['logins']['login_values'];
    }

}