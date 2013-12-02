<?php

$plugin = array(
  'schema' => 'transcoding_preset',
  'access' => 'administer transcodings',
  'menu' => array(
    'menu prefix' => 'admin/structure',
    'menu item' => 'transcoding-presets',
    'menu title' => 'Transcoding presets',
    'menu description' => 'Add, edit or delete custom transcoding presets',
  ),
  'title singular' => t('transcoding preset'),
  'title singular proper' => t('Transcoding preset'),
  'title plural' => t('transcoding presets'),
  'title plural proper' => t('Transcoding presets'),

  'handler' => 'transcoding_preset_ui',
);

