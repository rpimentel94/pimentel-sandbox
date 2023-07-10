<?php
namespace Drupal\custom_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Database\Database;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


/**
 * Class loremipsumForm.
 *
 * @package Drupal\loremipsum\Form
 */
class ExampleForm extends FormBase {
    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'example_form';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $queries = \Drupal::request()->query->all();

          $form['lead_id'] = array(
            '#type' => 'hidden',
            '#required' => TRUE,
            '#default_value' => $queries['lead_id'] ?: 11223322
        );

        $form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => t('First Name'),
            '#required' => TRUE,
            '#default_value' => $queries['first_name'] ?: ""
        );
        $form['last_name'] = array(
            '#type' => 'textfield',
            '#title' => t('Last Name'),
            '#required' => TRUE,
            '#default_value' => $queries['last_name'] ?: ""
        );
        $form['email'] = array(
            '#type' => 'textfield',
            '#title' => t('Email'),
            '#required' => TRUE,
            '#default_value' => $queries['email'] ?: ""
        );
        $form['phone'] = array(
            '#type' => 'textfield',
            '#title' => t('Phone'),
            '#required' => TRUE,
            '#attributes' => [
                "pattern" => "[0-9]{3}-[0-9]{3}-[0-9]{4}",
                'placeholder' => t('123-221-2212'),
                'maxlength' => 12
            ]);

        $form['body'] = array(
            '#type' => 'textarea',
            '#title' => t('Tell us what you think...'),
            '#required' => TRUE,
        );
        
        $form['submit'] = array (
            '#type' => 'submit',
            '#value' => t('Save'),
          );
      
          return $form;
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        // Validate email
        if (!\Drupal::service('email.validator')->isValid($form_state->getValues()['email'])) {
            $form_state->setErrorByName('Email', $this->t('Please enter a valid email address.'));
        }

        $phone_number_validation_regex = "\(?([2-9][0-8][0-9])\)?[-. ]?([2-9][0-9]{2})[-. ]?([0-9]{4})$";
        // Validate email
        if (preg_match($phone_number_validation_regex, $form_state->getValues()['phone'])) {
            $form_state->setErrorByName('Phone', $this->t('Please enter a valid US phone number.'));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $client = \Drupal::httpClient();

    $data = [];
    $data['lead_id'] = $form_state->getValue('lead_id');
    $data['first_name'] = $form_state->getValue('first_name');
    $data['last_name'] = $form_state->getValue('last_name');
    $data['email'] = $form_state->getValue('email');
    $data['phone'] = $form_state->getValue('phone');
    $data['body'] = $form_state->getValue('body');

    \Drupal::logger('my_module')->debug('<pre><code>' . print_r($data, TRUE) . '</code></pre>');
    \Drupal::messenger()->addMessage($this->t('Form Submitted Successfully'), 'status', TRUE);

    // try {
    //     $response = $client->post("https://testing/endpoint.com/api", [
    //       'form_params' => $data,
    //     ]);
    //     $response_data = json_decode($response->getBody()->getContents(), TRUE);
    
    //     // do something with data
    //   }
    //   catch (RequestException $e) {
    //     // log exception
    //   }


    }

/**
   *
   */
  public function setMessage(array $form, FormStateInterface $form_state) {

    

    $response = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result_message',
        '<div class="my_top_message">' . t('The results is @result', ['@result' => ($form_state->getValue('first_name'))]) . '</div>'),
    );
    return $response;

   }
}