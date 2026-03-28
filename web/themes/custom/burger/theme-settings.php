<?php

declare(strict_types=1);

/**
 * @file
 * Theme settings form for burger theme.
 */

use Drupal\Core\Form\FormState;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function burger_form_system_theme_settings_alter(array &$form, FormState $form_state): void {

  $form['burger'] = [
    '#type' => 'details',
    '#title' => t('burger'),
    '#open' => TRUE,
  ];

  $form['burger']['example'] = [
    '#type' => 'textfield',
    '#title' => t('Example'),
    '#default_value' => theme_get_setting('example'),
  ];

}
