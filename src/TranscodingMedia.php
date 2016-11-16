<?php

namespace Drupal\transcoding;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class TranscodingMedia.
 *
 * @package Drupal\transcoding
 */
class TranscodingMedia {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type_manager;
  /**
   * Constructor.
   */
  public function __construct(EntityTypeManager $entity_type_manager) {
    $this->entity_type_manager = $entity_type_manager;
  }

}
