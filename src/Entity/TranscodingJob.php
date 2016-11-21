<?php

namespace Drupal\transcoding\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\transcoding\Plugin\TranscoderPluginInterface;
use Drupal\transcoding\TranscodingJobInterface;
use Drupal\transcoding\TranscodingStatus;
use Drupal\user\UserInterface;

/**
 * Defines the Transcoding job entity.
 *
 * @ingroup transcoding
 *
 * @ContentEntityType(
 *   id = "transcoding_job",
 *   label = @Translation("Transcoding job"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\transcoding\TranscodingJobListBuilder",
 *     "views_data" = "Drupal\transcoding\Entity\TranscodingJobViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\transcoding\Form\TranscodingJobForm",
 *       "add" = "Drupal\transcoding\Form\TranscodingJobForm",
 *       "edit" = "Drupal\transcoding\Form\TranscodingJobForm",
 *       "delete" = "Drupal\transcoding\Form\TranscodingJobDeleteForm",
 *     },
 *     "access" = "Drupal\transcoding\TranscodingJobAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\transcoding\TranscodingJobHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "transcoding_job",
 *   admin_permission = "administer transcoding job entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/transcoding/jobs/transcoding_job/{transcoding_job}",
 *     "add-form" = "/admin/transcoding/jobs/transcoding_job/add",
 *     "edit-form" = "/admin/transcoding/jobs/transcoding_job/{transcoding_job}/edit",
 *     "delete-form" = "/admin/transcoding/jobs/transcoding_job/{transcoding_job}/delete",
 *     "collection" = "/admin/transcoding/jobs/transcoding_job",
 *   }
 * )
 */
class TranscodingJob extends ContentEntityBase implements TranscodingJobInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Transcoding job entity.'))
      ->setReadOnly(TRUE);
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Transcoding job entity.'))
      ->setReadOnly(TRUE);

    $fields['service'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Service'))
      ->setSetting('target_type', 'transcoding_service')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE);
    $fields['media_bundle'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Target Bundle'))
      ->setSetting('target_type', 'media_bundle')
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 2,
      ])
      ->setDisplayConfigurable('view', TRUE);
    $fields['media_target_field'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Target field'))
      ->setDescription(t('Target field on selected bundle'))
      ->setSettings(array(
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['service_data'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Service data'))
      ->setReadOnly(TRUE)
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE);
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Transcoding job entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Status'))
      ->setDescription(t('The job status'))
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue(TranscodingStatus::PENDING)
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * @return TranscoderPluginInterface
   */
  public function getPlugin() {
    return TranscodingService::load($this->service->getString())->getPlugin();
  }

  /**
   * Process the job through the plugin specified at creation.
   */
  public function process() {
    $this->getPlugin()->processJob($this);
  }

  /**
   * Get the service data the plugin previously stored on this job.
   *
   * @return array
   */
  public function getServiceData() {
    return $this->service_data->first()->getValue();
  }

}
