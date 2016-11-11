<?php

namespace Drupal\transcoding\Plugin;

use Drupal\Component\Plugin\PluginBase;

abstract class TranscoderBase extends PluginBase implements TranscoderPluginInterface {

  /**
   * @inheritDoc
   */
  public function getConfiguration() {
    return $this->configuration;
  }
  /**
   * @inheritDoc
   */

  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration;
  }

}
