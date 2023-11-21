<?php

namespace Drupal\hello_world\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hello_world\Services\LastFM;

/**
 * Form for LastFM authentication.
 *
 * @package Drupal\hello_world\Form
 */
class AuthLastFMForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_auth_lastfm';
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!$form_state->getValue('auth_confirmation')) {
      $form_state->setErrorByName('auth_confirmation', $this->t('You must grant access to the LastFM API in order to continue.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // drupal_set_message($this->t('@can_name ,Your application is being submitted!', array('@can_name' => $form_state->getValue('candidate_name'))));
    // foreach ($form_state->getValues() as $key => $value) {
    //   \Drupal::messenger()->addMessage($key . ': ' . $value);
    // }





    // Step 4 - Fetch web service session.
    // TODO: need a persistent storage for the request token, so it can be grabbed
    // on a second visit and used for the session request.
    // $session_request = [
    //   'api_key' => $api_key,
    //   'method' => 'auth.getSession',
    //   'token' => $token,
    // ];
    // $session_response = $this->request($session_request);


    $lfm = new LastFM();
    $request_token = $lfm->fetchRequestToken();
    $api_key = $lfm->apiKey;

    $session = $this->getRequest()->getSession();
    $session->set('lfm_req_token', $request_token);

    // TODO: Steps 3/4 need to be broken into separate pages/form/steps.
    // If the user does not authorize the token the session req is not valid.
    // Step 3 - request user authorization (only once)
    $redirect = "http://www.last.fm/api/auth/?api_key={$api_key}&token={$request_token}";
    $content = "Visit {$redirect} to authorize the application.";

    $request_markup = [
      '#type' => 'markup',
      '#markup' => $content,
    ];
    \Drupal::messenger()->addMessage($request_markup);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // $form['candidate_name'] = array(
    //   '#type' => 'textfield',
    //   '#title' => t('Candidate Name:'),
    //   '#required' => TRUE,
    // );
    // $form['candidate_mail'] = array(
    //   '#type' => 'email',
    //   '#title' => t('Email ID:'),
    //   '#required' => TRUE,
    // );
    // $form['candidate_number'] = array (
    //   '#type' => 'tel',
    //   '#title' => t('Mobile no'),
    // );
    // $form['candidate_dob'] = array (
    //   '#type' => 'date',
    //   '#title' => t('DOB'),
    //   '#required' => TRUE,
    // );
    // $form['candidate_gender'] = array (
    //   '#type' => 'select',
    //   '#title' => ('Gender'),
    //   '#options' => array(
    //     'Female' => t('Female'),
    //     'male' => t('Male'),
    //   ),
    // );
    $form['auth_confirmation'] = array (
      '#type' => 'radios',
      '#title' => ('Do you want to grant this application read access to your LastFM account?'),
      '#options' => [
        TRUE => t('Accept'),
        FALSE => t('Decline'),
      ],
    );
    // $form['candidate_copy'] = array(
    //   '#type' => 'checkbox',
    //   '#title' => t('Send me a copy of the application.'),
    // );
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    );
    return $form;
  }

}
