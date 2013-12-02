<?php


function transcoding_rules_data_info() {
  return array(
    'transcoding_preset' => array(
      'label' => t('transcoding preset'),
      'ui class' => 'RulesDataUITranscodingPreset',
    ),
  );
}



class RulesDataUITranscodingPreset extends RulesDataUIText {

  public static function inputForm($name, $info, $settings, RulesPlugin $element) {
    $form = parent::inputForm($name, $info, $settings, $element);
    if (empty($info['options list'])) {
      $form[$name]['#type'] = 'select';
    }

    $presets = array();
    foreach (transcoding_load_all_presets() as $preset_name => $preset) {
      $presets[$preset_name] = $preset->title;
    }
    $form[$name]['#options'] = $presets;
    return $form;
  }
}

function transcoding_rules_action_info() {
  return array(
    'transcode_file' => array(
      'label' => t('Transcode file'),
      'parameter' => array(
        'source' => array('type' => 'file', 'label' => t('Source file')),
        'destination' => array(
          'type' => 'text',
          'label' => t('Destination filename'),
          'description' => 'The filename to encode to, without the file extension (which comes from the preset).',
        ),
        'preset' => array('type' => 'transcoding_preset', 'label' => t('Preset')),
        'context' => array('type' => 'entity', 'label' => t('Context')),
      ),
      'group' => t('Transcoding'),
      'base' => 'transcoding_action_transcode_file',
    ),
  );
}

function transcoding_action_transcode_file($file, $destination, $preset, $context) {
  $preset = transcoding_load_preset($preset);
  $destination = "/tmp/{$destination}." . $preset->settings['extension'];
  $item = array(
    'source_uri' => $file->uri,
    'destination_file' => $destination,
    'preset' => $preset->name,
    'context' => $context,
    'timestamp' => REQUEST_TIME,
    'status' => 'pending',
  );
  $result = drupal_write_record('transcoding_queue', $item);
  if (!$result) {
    watchdog('transcoding', 'There was a problem submitting the transcoding job to the queue');
  }
}

function transcoding_rules_event_info() {
  return array(
    'transcoding_done' => array(
      'label' => t('Transcoding done'),
      'group' => t('Transcoding'),
      'variables' => array(
        'context' => array(
          'type' => 'entity', 'label' => t('Context'),
        ),
        'file' => array(
          'type' => 'file', 'label' => t('Transcoded file'),
        ),
      ),
    ),
  );
}
