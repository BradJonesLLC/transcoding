<?php

namespace Drupal\transcoding\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Transcoding job edit forms.
 *
 * @ingroup transcoding
 */
class TranscodingJobForm extends ContentEntityForm {
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\transcoding\Entity\TranscodingJob */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Transcoding job.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Transcoding job.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('entity.transcoding_job.canonical', ['transcoding_job' => $entity->id()]);
  }

}
