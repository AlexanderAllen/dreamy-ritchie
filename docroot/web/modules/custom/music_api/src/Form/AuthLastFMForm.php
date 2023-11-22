<?php

namespace Drupal\music_api\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\{FormBase, FormStateInterface};
use Drupal\Core\State\State;
use Drupal\music_api\Service\LastFM;

/**
 * Form for LastFM authentica tion.
 *
 * @package Drupal\music_api\Form
 */
class AuthLastFMForm extends FormBase {

  /**
   * Last FM Auth Service.
   *
   * @var Drupal\music_api\Services\LastFM
   */
  protected LastFM $lfmService;
  protected State $state;
  protected LastFM $lfm;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'form_auth_lastfm';
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(State $state, LastFM $lfmService) {
    $this->state = $state;
    $this->lfm = $lfmService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('state'),
      $container->get('Drupal\music_api\Service\LastFM'),
    );
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
    $token = $this->lfm->fetchRequestToken();

    $form_state
      ->set('page_num', 2)
      ->set('req_token', $token)
      ->set('api_key', $this->lfm->apiKey)
      ->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Check if authorization exists already before proceeding.
    if (!empty($this->state->get('lfm_session_key'))) {
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
    $session_key = $this->lfm->fetchSessionKey($request_token);
    $this->state->set('lfm_session_key', $session_key);
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
      // builder to rebuild the form.
      ->setRebuild(TRUE);
  }

}
