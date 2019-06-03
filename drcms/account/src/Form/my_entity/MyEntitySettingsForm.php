<?php

namespace Drupal\account\Form\my_entity;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class MyEntitySettingsForm.
 *
 * @package Drupal\account\Form
 *
 * @ingroup account
 */
class MyEntitySettingsForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'MyEntity_settings';
  }

  /**
   * Form submission handler. It saves the settings configuration of the entity
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValues()['arc_entity_redirect'];
    $this->configFactory()->getEditable('account.my_entity.settings')
      ->set('arc_entity_redirect', $value)
      ->save();
  }

  /**
   * Defines the settings form for the current entity.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form[$this->getFormId()]['arc_entity_redirect'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow redirect to edit form after submitting of add/edit form.'),
      '#default_value' => $this->configFactory()->getEditable('account.my_entity.settings')->get('arc_entity_redirect'),
    ];

    $form['actions']= [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

}
