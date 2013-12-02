<?php

function transcode_thumb_select($form, &$form_state, $node) {
  // $debug = '<pre>' . print_r(get_defined_vars(), TRUE) . '</pre>';
  $form = array();
  // $form['debug'] = array('#markup' => $debug);

  //$duration_field = variable_get('codem_video_duration_field', '');
  $duration_field = 'field_duration';

  $duration = $node->{$duration_field}[LANGUAGE_NONE][0]['value'] * 100;
  if (empty($duration)) {
    $duration = codem_get_film_length($node, 'field_film_file') * 100;
  }
  $no_preset = 6;
  $thumb_strip = '<div style="margin-right:100px;height:70px"><div id="thumb-strip" style="position:relative;width:100%;">';
  for ($i = 300; $i <= $duration; $i += (($duration - 600) / ($no_preset - 1))) {
    $preview_frame = codem_thumb_generator($node, intval($i), TRUE);
    $thumb_strip .= '<img data-frame="' . $i . '" src="' . $preview_frame . '"
       style="width:100px;position:absolute;left:' . (($i / $duration) * 100) . '%" />';
  }
  $thumb_strip .= '</div></div>';
  $form['thumb_select'] = array(
    '#type' => 'textfield',
    '#title' => 'Preview image offset',
    '#default_value' => '300',
    '#description' => 'Select the frame index (in hundreths of a second). The maximum for this film is ' . $duration,
    '#prefix' => '<div id="admin-thumb-choice"><div id="thumb-throbber" style="display:none;float:right;" class="ajax-progress"><span class="throbber"></span></div>',
    '#suffix' => $thumb_strip . '<div id="slider" style="margin:0 50px;" data-node="' . $node->nid . '" data-duration="' . $duration . '"></div><div id="thumb-preview" style="text-align:center;margin-top:20px;"></div></div>',
    '#attached' => array(
      'js' => array(
        drupal_get_path('module', 'transcoding') . '/js/codem-thumb.js',
      ),
      'library' => array(
        array('system', 'ui.slider'),
      ),
    ),
  );
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => 'Update',
  );
  return $form;
}

function transcode_thumb_select_submit($form, $form_state) {
  $node = $form_state['build_info']['args'][0];
  $frame = $form_state['values']['thumb_select'];
  // debug($frame);
  $instances = field_info_instances('node', $node->type);
  $image_field = variable_get('codem_video_image_field', '');
  $image_field = 'field_film_thumbnail';
  $film_fields = array('field_film_file');
  //$film_fields = variable_get('codem_video_file_fields', array());
  foreach ($film_fields as $field) {
    if (in_array($field, array_keys($instances))) {
      if (in_array($image_field, array_keys($instances))) {
        $file = codem_get_film_thumbnail($node, $field, variable_get('codem_video_thumbnail_default', $frame));
        $node->{$image_field}[LANGUAGE_NONE][0]['fid'] = $file;
      }
    }
  }
  node_save($node);
  drupal_goto('node/' . $node->nid);
}

