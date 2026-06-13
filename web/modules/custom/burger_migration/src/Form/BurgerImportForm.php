<?php

namespace Drupal\burger_migration\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Provides a form for importing burgers from CSV.
 */
class BurgerImportForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'burger_migration_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = [
      '#markup' => '<p>' . $this->t('Upload a CSV file to import Burger content. The CSV should have headers: <strong>title, summary, price, menu_type</strong>.') . '</p>',
    ];

    $form['csv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('CSV File'),
      '#upload_location' => 'public://burger_imports/',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
      ],
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Import Burgers'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $file_id_array = $form_state->getValue('csv_file');
    if (!empty($file_id_array[0])) {
      $file = File::load($file_id_array[0]);
      if ($file) {
        $file->setPermanent();
        $file->save();

        $file_path = $file->getFileUri();

        // Process the CSV in a batch.
        $batch = [
          'title' => $this->t('Importing Burgers...'),
          'operations' => [
            ['\Drupal\burger_migration\BurgerImportBatch::processCsv', [$file_path]],
          ],
          'finished' => '\Drupal\burger_migration\BurgerImportBatch::finished',
        ];

        batch_set($batch);
      }
    }
  }

}
