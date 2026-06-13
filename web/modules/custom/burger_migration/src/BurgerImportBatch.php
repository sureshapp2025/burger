<?php

namespace Drupal\burger_migration;

use Drupal\node\Entity\Node;

/**
 * Batch processing for burger import.
 */
class BurgerImportBatch {

  /**
   * Batch operation to process the CSV file.
   */
  public static function processCsv($file_path, &$context) {
    if (!isset($context['sandbox']['progress'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['current_row'] = 0;
      $context['sandbox']['max'] = 0;

      // Count total rows.
      if (($handle = fopen($file_path, 'r')) !== FALSE) {
        while (fgetcsv($handle) !== FALSE) {
          $context['sandbox']['max']++;
        }
        fclose($handle);
      }
      
      // Subtract header row.
      $context['sandbox']['max'] = max(0, $context['sandbox']['max'] - 1);
    }

    $limit = 50; // Process 50 rows per batch.
    $processed = 0;

    if (($handle = fopen($file_path, 'r')) !== FALSE) {
      // Get headers.
      $headers = fgetcsv($handle);
      if ($headers) {
        $headers = array_map('trim', $headers);
      }
      
      // Skip already processed rows.
      for ($i = 0; $i < $context['sandbox']['progress']; $i++) {
        fgetcsv($handle);
      }

      while (($data = fgetcsv($handle)) !== FALSE) {
        if (count($headers) == count($data)) {
          $row = array_combine($headers, $data);
          
          $title = trim($row['title'] ?? '');
          if (!empty($title)) {
            // Create the node.
            $node_data = [
              'type' => 'burger',
              'title' => $title,
            ];
            
            if (isset($row['summary'])) {
               $node_data['field_summary'] = ['value' => $row['summary']];
            }
            if (isset($row['price'])) {
               $node_data['field_price'] = ['value' => $row['price']];
            }
            if (isset($row['menu_type'])) {
               $node_data['field_menu_types'] = ['value' => $row['menu_type']];
            }
            
            $node = Node::create($node_data);
            $node->save();
          }
        }
        
        $context['sandbox']['progress']++;
        $context['sandbox']['current_row']++;
        $processed++;
        
        if ($processed >= $limit) {
          break;
        }
      }
      fclose($handle);
    }

    if ($context['sandbox']['progress'] != $context['sandbox']['max'] && $context['sandbox']['max'] > 0) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
    else {
      $context['finished'] = 1;
    }

    $context['message'] = t('Imported @count of @total burgers.', [
      '@count' => $context['sandbox']['progress'],
      '@total' => $context['sandbox']['max'],
    ]);
  }

  /**
   * Batch finished callback.
   */
  public static function finished($success, $results, $operations) {
    if ($success) {
      \Drupal::messenger()->addMessage(t('Burger import completed successfully.'));
    }
    else {
      \Drupal::messenger()->addError(t('An error occurred during the import.'));
    }
  }

}
