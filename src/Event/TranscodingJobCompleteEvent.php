<?php

namespace Drupal\transcoding;

use Drupal\media_entity\MediaInterface;
use Drupal\transcoding\Event\TranscodingJobEvent;

/**
 * Class TranscodingJobCompleteEvent
 * @package Drupal\transcoding
 */
class TranscodingJobCompleteEvent extends TranscodingJobEvent {

  /**
   * The media slated for creation.
   *
   * @var \Drupal\media_entity\MediaInterface
   */
  protected $media;

  /**
   * @inheritDoc
   */
  public function __construct(TranscodingJobInterface $job, MediaInterface $media) {
    parent::__construct($job);
    $this->media = $media;
  }

  /**
   * Media getter.
   * @return \Drupal\media_entity\MediaInterface
   */
  public function getMedia() {
    return $this->media;
  }

}
