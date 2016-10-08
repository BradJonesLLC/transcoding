<?php

namespace Drupal\transcoding\Plugin;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;

abstract class TranscoderBase extends PluginBase implements ConfigurablePluginInterface, PluginFormInterface {

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
