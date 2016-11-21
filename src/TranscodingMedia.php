<?php

namespace Drupal\transcoding;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\media_entity\Entity\Media;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class TranscodingMedia.
 *
 * @package Drupal\transcoding
 */
class TranscodingMedia {

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type_manager;

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructor.
   */
  public function __construct(EntityTypeManager $entity_type_manager, EventDispatcherInterface $eventDispatcher) {
    $this->entity_type_manager = $entity_type_manager;
    $this->eventDispatcher = $eventDispatcher;
  }

  protected function buildMedia(TranscodingJobInterface $job, $uri) {
    $media = Media::create([
      'bundle' => $job->media_bundle->entity->id(),
      'uid' => $job->getOwnerId(),
      'name' => $job->label(),
    ]);

    return $media;
  }

  public function complete(TranscodingJobInterface $job, $uri) {
    $media = $this->buildMedia($job, $uri);
    $event = new TranscodingJobCompleteEvent($job, $media);
    $this->eventDispatcher->dispatch(TranscodingJobEvents::COMPLETE, $event);
    $media->save();
  }

}
