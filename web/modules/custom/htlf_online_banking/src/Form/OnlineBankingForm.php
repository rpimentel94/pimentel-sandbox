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

        //dd($config);

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
                'callback' => '::updateLoginCallback',
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
                'callback' => '::updateLoginCallback',
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

        $number_of_logins = !empty($config->get('login_settings')) && empty($form_state->get('number_of_logins')) ? count($config->get('login_settings')) : $form_state->get('number_of_logins');
        $login_settings = $config->get('login_settings');

        $values = [
            'link' => t('Link'),
            'form' => t('Form')
        ];

        //echo "Counter: " . $number_of_logins;

        if (empty($number_of_logins)) {
            $number_of_logins = 1;
            $form_state->set('number_of_logins', $number_of_logins);
        }

        //echo "<hr> Counter: " . $number_of_logins . " " . $form_state->get('number_of_logins');

        for ($i = 1; $i <= $number_of_logins; $i++) {

            $index = $i - 1;

            $form['logins']['login_values'][$i]['container'] = [
                '#type' => 'container',
                '#tree' => TRUE,
                '#prefix' => '<div id="individual-items">',
                '#suffix' => '</div>',
            ];

            $form['logins']['login_values'][$i]['container']['name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Name'),
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['name'] : "",
            ];

            $form['logins']['login_values'][$i]['container']['active'] = array(
                '#type' => 'checkbox',
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['active'] : FALSE,
                '#title' => t('Active'),
                '#required' => FALSE,
            );

            $form['logins']['login_values'][$i]['container']['type'] = array(
                '#title' => t('Login Type'),
                '#type' => 'select',
                '#options' => $values,
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['type'] : "link",
            );

            $form['logins']['login_values'][$i]['container']['url'] = [
                '#type' => 'textfield',
                '#title' => $this->t('URL'),
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['url'] : "url",
            ];

            $form['logins']['login_values'][$i]['container']['form_action'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Form Action'),
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['form_action'] : "",
            ];

            $form['logins']['login_values'][$i]['container']['user_name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('User ID Name Override'),
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['user_name'] : "",
            ];

            $form['logins']['login_values'][$i]['container']['pass_name'] = [
                '#type' => 'textfield',
                '#title' => $this->t('Password Name Override'),
                '#default_value' => isset($login_settings[$index]) ? $login_settings[$index]['pass_name'] : "",
            ];
        }

        return parent::buildForm($form, $form_state);
    }

    /** 
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $login_values = $form_state->getValue('login_values');
        $login_settings = [];
        foreach ($login_values as $index => $login_items) {
            $login_settings[$index] = $login_items['container'];
        }

        $this->config(static::SETTINGS)
            // Set the submitted configuration setting.
            ->set('default_title', $form_state->getValue('default_title'))
            ->set('enable_logins', $form_state->getValue('enable_logins'))
            ->set('login_settings', $login_settings)
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
     * Return the login field list (Form).
     */
    public function updateLoginCallback(array &$form, FormStateInterface $form_state)
    {
        return $form['logins']['login_values'];
    }

}