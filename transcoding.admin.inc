<?php

function transcoding_overview_page() {
  transcoding_cron();
  $form = array();
  $form['help'] = array(
    '#markup' => '<div>Transcoding presets are managed on the <a href="/admin/structure/transcoding-presets">presets</a> page.<br />' . 
        'Transcoding server: ' . variable_get('transcoding_server', 'http://localhost:8080') . '</div>',
  );
  if ($status = transcoding_get_status()) {
    $form['jobs']['slots'] = array(
      '#prefix' => '<div id="transcoding-status">',
      '#suffix' => '</div>',
    );
    for ($delta = 0; $delta < $status->max_slots; $delta++) {
      $form['jobs']['slots'][$delta] = array(
        '#type' => 'fieldset',
        '#title' => 'Transcoder slot #' . ($delta + 1),
          'progress' => array(
          '#markup' => theme('progress_bar', array(
            'percent' => !empty($status->jobs[$delta]->progress) ? floor($status->jobs[$delta]->progress * 100) : 0,
            'message' => !empty($status->jobs[$delta]->status) ? check_plain($status->jobs[$delta]->status) : 'ready and waiting',
          )),
        ),
      );
    }
    $form['jobs']['refresh'] = array(
      '#type' => 'button',
      '#value' => 'Refresh status',
      '#ajax' => array(
        'callback' => 'transcoding_ajax_status_callback',
        'wrapper' => 'transcoding-status',
      ),
    );
  }
  else {
    $form['status'] = array(
      '#type' => 'fieldset',
      '#title' => 'Transcoder status',
      'message' => array(
        '#markup' => '<div>Transcoder offline</div>',
      ),
    );
  }

  $jobs = db_select('transcoding_queue', 'q')
    ->fields('q')
    ->execute()
    ->fetchAllAssoc('qid');

  $form['queue'] = array(
    '#prefix' => '<div id="transcoding-queue-table-wrapper">',
    '#suffix' => '</div>',
    '#id' => 'transcoding-queue-table',
    '#tree' => TRUE,
    '#theme' => 'transcoding_queue_table',
  );
  if (!empty($jobs)) {
    foreach ($jobs as $qid => $job) {
      $entity = unserialize($job->context);
      $type = $entity->type();
      $id = $entity->getIdentifier();
      $entity = reset(entity_load($type, array($id)));
      $d = entity_uri($type, $entity);
      $form['queue'][$qid]['context'] = array(
        '#markup' => l(entity_label($type, $entity), $d['path']),
      );
      $form['queue'][$qid]['source_uri'] = array(
        '#markup' => $job->source_uri
      );
      $form['queue'][$qid]['destination_file'] = array(
        '#markup' => $job->destination_file,
      );
      $form['queue'][$qid]['preset'] = array(
        '#markup' => $job->preset,
      );
      $form['queue'][$qid]['status'] = array(
        '#markup' => $job->status,
      );
      $form['queue'][$qid]['timestamp'] = array(
        '#markup' => format_date($job->timestamp, 'short'),
      );
      $form['queue'][$qid]['operations'] = array(
        array(
          '#type' => 'submit',
          '#qid' => $qid,
          '#name' => 'delete-' . $qid,
          '#op' => 'delete',
          '#access' => user_access('administer transcoding queue'),
          '#value' => 'Delete',
        ),
        array(
          '#type' => 'submit',
          '#qid' => $qid,
          '#name' => 'redo-' . $qid,
          '#op' => 'redo',
          '#access' => user_access('administer transcoding queue'),
          '#value' => 'Redo',
        ),
      );
    }
  }

  /*
  foreach ($jobs as $job) {
    $form['queue_debug'][] = array(
      '#markup' => '<pre>' . print_r($job, TRUE) . '</pre>',
    );
  }
  */

  return $form;
}

function transcoding_overview_page_submit($form, &$form_state) {
  $element = $form_state['triggering_element'];
  if ($result = transcoding_queue_operation($element['#op'], $element['#qid'])) {
    drupal_set_message("Done: {$element['#op']} for item with ID {$element['#qid']}");
  }
  else {
    drupal_set_message("There was an error processing your request");
  }
}

function transcoding_ajax_status_callback(&$form, &$form_state) {
  return $form['jobs']['slots'];
}



