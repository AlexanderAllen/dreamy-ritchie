<?php

// phpcs:disable Drupal.Commenting.InlineComment.SpacingAfter
// phpcs:disable DrupalPractice.Commenting.CommentEmptyLine.SpacingAfter
// phpcs:disable Drupal.Files.LineLength.TooLong

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
   * Last FM Auth Service.
   *
   * @var Drupal\hello_world\Services\LastFM
   */
  protected LastFM $lfmService;

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
    // if (!$form_state->getValue('auth_confirmation')) {
    //   $form_state->setErrorByName('auth_confirmation', $this->t('You must grant access to the LastFM API in order to continue.'));
    // }

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // @todo needs to be moved to a formal Service object for dependency injection.
    $lfm = new LastFM();
    $token = $lfm->fetchRequestToken();

    $form_state
      ->set('page_num', 2)
      ->set('req_token', $token)
      ->set('api_key', $lfm->apiKey)
      ->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Check if authorization exists already before proceeding.
    $session = $this->getRequest()->getSession();
    if (!empty(\Drupal::state()->get('lfm_session_key'))) {
      $form['description'] = [
        '#type' => 'item',
        '#title' => $this->t('Application authorized.'),
      ];
      return $form;
    }

    if ($form_state->has('page_num') && $form_state->get('page_num') == 2) {
      return $this->authorizePageTwo($form, $form_state);
    }

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 1)'),
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['auth_confirmation'] = [
      '#type' => 'checkbox',
      '#title' => ('Would you like to grant this application read access to your LastFM account?'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Accept'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * Step two form.
   */
  public function authorizePageTwo(array &$form, FormStateInterface $form_state) {
    // @todo 1:30PM - move this into state and display on second step of form.
    // 2:30pm done
    $api_key = $form_state->get('api_key', '');
    $request_token = $form_state->get('req_token', '');

    // // Step 3 - request user authorization (only once)
    $redirect = "http://www.last.fm/api/auth/?api_key={$api_key}&token={$request_token}";
    $content = "Please visit <a href='{$redirect}'>LastFM</a> in order to authorize the application.";

    $request_markup = [
      '#type' => 'markup',
      '#markup' => $content,
    ];

    $form['description'] = [
      '#type' => 'item',
      '#title' => $this->t('A basic multistep form (page 2)'),
      '#description' => $request_markup,
    ];

    $form['back'] = [
      '#type' => 'submit',
      '#value' => $this->t('Back'),
      // Custom submission handler for 'Back' button.
      '#submit' => ['::subscribePageTwoBack'],
      '#limit_validation_errors' => [],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#button_type' => 'primary',
      '#value' => $this->t('Next'),
      '#submit' => ['::authorizePageTwoSubmit'],
    ];

    return $form;
  }

  /**
   * Step 4 - Fetch session key using request token.
   */
  public function authorizePageTwoSubmit(array &$form, FormStateInterface $form_state) {

    $request_token = $form_state->get('req_token', '');

    $lfm = new LastFM();
    $session_key = $lfm->fetchSessionKey($request_token);

    // Save session key to persistent storage.
    // @todo Swtich to dependency injection here.
    \Drupal::state()->set('lfm_session_key', $session_key);
  }

  /**
   * Stub comment.
   */
  public function subscribePageTwoBack(array &$form, FormStateInterface $form_state) {
    $form_state
      // Restore values for the first step.
      ->setValues($form_state->get('page_values'))
      ->set('page_num', 1)
      // Since we have logic in our buildForm() method, we have to tell the form
      // builder to rebuild the form. Otherwise, even though we set 'page_num'
      // to 1, the AJAX-rendered form will still show page 2.
      ->setRebuild(TRUE);
  }

}
