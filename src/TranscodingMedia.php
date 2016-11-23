<?php

namespace Drupal\transcoding;

use Drupal\file\Entity\File;
use Drupal\media_entity\Entity\Media;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\transcoding\Event\TranscodingJobCompleteEvent;

/**
 * Class TranscodingMedia.
 *
 * @package Drupal\transcoding
 */
class TranscodingMedia {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructor.
   */
  public function __construct(EventDispatcherInterface $eventDispatcher) {
    $this->eventDispatcher = $eventDispatcher;
  }

  protected function buildMedia(TranscodingJobInterface $job) {
    $media = Media::create([
      'bundle' => $job->media_bundle->entity->id(),
      'uid' => $job->getOwnerId(),
    ]);
    return $media;
  }

  public function complete(TranscodingJobInterface $job, $uri) {
    $media = $this->buildMedia($job);
    $event = new TranscodingJobCompleteEvent($job, $media);
    $this->eventDispatcher->dispatch(TranscodingJobEvents::COMPLETE, $event);
    // If none of the processing has yet set a file (e.g., after moving), set now.
    if ($media->get($job->media_target_field->getString())->isEmpty()) {
      $file = File::create([
        'uri' => $uri,
      ]);
      $file->save();
      $media->set($job->media_target_field->getString(), [
        'target_id' => $file->id(),
      ]);
    }
    $media->save();
    $job->set('status', TranscodingStatus::COMPLETE)
      ->set('media', ['target_id' => $media->id()])
      ->save();
  }

}
