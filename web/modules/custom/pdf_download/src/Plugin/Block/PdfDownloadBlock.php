<?php

declare(strict_types=1);

namespace Drupal\pdf_download\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a pdf download block.
 */
#[Block(
  id: 'pdf_download_pdf_download',
  admin_label: new TranslatableMarkup('pdf download'),
  category: new TranslatableMarkup('Custom'),
)]
final class PdfDownloadBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build['content'] = [
      '#markup' => $this->t('It works!'),
    ];
    return $build;
  }

}
