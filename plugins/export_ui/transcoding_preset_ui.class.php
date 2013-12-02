<?php

class transcoding_preset_ui extends ctools_export_ui {

  function edit_form(&$form, &$form_state) {
    parent::edit_form($form, $form_state);

    $vcodecs = transcoding_get_video_encoders();
    $acodecs = transcoding_get_audio_encoders();
    asort($vcodecs);
    asort($acodecs);
    $defaults = $form_state['item']->settings;

    $form['extension'] = array(
      '#type' => 'textfield',
      '#field_prefix' => '.',
      '#size' => 10,
      '#title' => 'File extension',
      '#default_value' => isset($defaults['extension']) ? $defaults['extension'] : '',
    );

    $form['video'] = array('#type' => 'fieldset', '#title' => 'Video settings');
    $form['video']['vcodec'] = array(
      '#type' => 'select',
      '#title' => 'Video codec',
      '#options' => $vcodecs,
      '#default_value' => isset($defaults['vcodec']) ? $defaults['vcodec'] : NULL,
    );
    $form['video']['b'] = array(
      '#type' => 'textfield',
      '#title' => 'Video bitrate',
      '#description' => 'Enter a new bitrate for the output video, or leave empty to use the source bitrate.',
      '#size' => 10,
      '#field_suffix' => 'bps',
      '#default_value' => isset($defaults['b']) ? $defaults['b'] : NULL,
    );
    $form['video']['bt'] = array(
      '#type' => 'textfield',
      '#title' => 'Tolerance',
      '#size' => 10,
      '#field_suffix' => 'bits',
      '#default_value' => isset($defaults['bt']) ? $defaults['bt'] : NULL,
    );
    $form['video']['r'] = array(
      '#type' => 'textfield',
      '#title' => 'Frame rate',
      '#size' => 10,
      '#field_suffix' => 'frames/sec',
      '#default_value' => isset($defaults['r']) ? $defaults['r'] : NULL,
    );
    $form['video']['g'] = array(
      '#type' => 'textfield',
      '#title' => 'GOP',
      '#size' => 10,
      '#description' => 'Group of pictures settings. Number of frames between keyframes.',
      '#default_value' => isset($defaults['g']) ? $defaults['g'] : NULL,
    );

    $form['audio'] = array('#type' => 'fieldset', '#title' => 'Audio settings');
    $form['audio']['acodec'] = array(
      '#type' => 'select',
      '#title' => 'Audio codec',
      '#options' => $acodecs,
      '#default_value' => isset($defaults['acodec']) ? $defaults['acodec'] : NULL,
    );
    $form['audio']['ab'] = array(
      '#type' => 'textfield',
      '#title' => 'Audio bitrate',
      '#description' => 'Enter a new bitrate for the output audio, or leave empty to use the source bitrate.',
      '#size' => 10,
      '#field_suffix' => 'bps',
      '#default_value' => isset($defaults['ab']) ? $defaults['ab'] : NULL,
    );
    $form['audio']['ac'] = array(
      '#type' => 'textfield',
      '#title' => 'Audio channels',
      '#description' => 'Number of audio channels (2 for stereo).',
      '#size' => 10,
      '#default_value' => isset($defaults['ac']) ? $defaults['ac'] : 2,
    );

    $width = ''; $height = '';
    if (!empty($defaults['s'])) {
      list($width, $height) = explode('x', $defaults['s']);
    }
    $form['size'] = array('#type' => 'fieldset', '#title' => 'Output size');
    $form['size']['width'] = array(
      '#type' => 'textfield',
      '#title' => 'Width',
      '#size' => 10,
      '#field_suffix' => 'px',
      '#default_value' => isset($width) ? $width : NULL,
    );
    $form['size']['height'] = array(
      '#type' => 'textfield',
      '#title' => 'Height',
      '#size' => 10,
      '#field_suffix' => 'px',
      '#default_value' => isset($height) ? $height : NULL,
    );

    $options = array(
      '-movflags +faststart' => 'QT-Faststart',
    );
    $form['options'] = array('#type' => 'fieldset', '#title' => 'Options');
    $form['options']['misc'] = array(
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => isset($defaults['options']) ? $defaults['options'] : array(),
    );

  }

  function edit_form_submit(&$form, &$form_state) {
    // debug($form_state['values']);
    $form_state['values']['settings'] = array(
      'extension' => $form_state['values']['extension'],
      'vcodec' => $form_state['values']['vcodec'],
      'b' => $form_state['values']['b'],
      'bt' => $form_state['values']['bt'],
      'r' => $form_state['values']['r'],
      'g' => $form_state['values']['g'],
      'acodec' => $form_state['values']['acodec'],
      'ab' => $form_state['values']['ab'],
      'ac' => $form_state['values']['ac'],
      's' => $form_state['values']['width'] . 'x' . $form_state['values']['height'],
      'options' => $form_state['values']['misc'],
    );
    $form_state['values']['settings']['encoder_options'] =
      $this->enc($form_state['values']['settings']);
    parent::edit_form_submit($form, $form_state);
  }

  function enc($values) {
    $options = $values['options'];
    unset($values['options']);
    unset($values['extension']);
    $params = array();
    $values = array_filter($values);
    foreach ($values as $key => $value) {
      $params[] = "-{$key} {$value}";
    }
    foreach ($options as $key => $value) {
      $params[] = $key;
    }
    return implode(' ', $params);
  }

}
