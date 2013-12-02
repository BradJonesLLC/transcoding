Drupal Transcoding
==================

Module that provides a transcoding service for Drupal. Encoding presets can be
defined in code, or via the UI. Encoding presets are exportable via ctools
exportables.

Currently the module provides a simple queuing system for managing transcoding
jobs, but there are plans to add more advanced queuing and scheduling options
in future versions.

Transcoding jobs can be configured via the API. Or, using the provided Rules
integration to configure them via the UI.


Codem Transcode
---------------

Drupal Transcoding uses Codem Transcode for asynchronious (offline) video
transcoding. Codem Transcode is written in node.js. It uses ffmpeg for
transcoding and has a simple HTTP API.

    https://github.com/madebyhiro/codem-transcode

Codem Transcode requires ffmpeg, sqlite3, and node.js (version > 0.8.11) with
packages for sqlite3, express, argsparser, and mkdirp. Refer to INSTALL.txt
for more information on getting up and running with Codem Transcode and
Drupal Transcoding.

Install latest node in Ubuntu 12.04
https://gist.github.com/2983873

Run Codem Transcode as a service:
https://gist.github.com/3640460


Encoding Presets
----------------

Transcoding presets can be configured via the UI. They can also be
defined in code using the defaults hook.


Transcoding Queue
-----------------

The module maintains a queue of submitted jobs and will submit them to the
Codem Transcode backend on CRON where they run asyncroniously. Jobs can be
submitted to the queue programmatically, or configured via Rules integration.

