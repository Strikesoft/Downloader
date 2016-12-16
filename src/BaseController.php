<?php
namespace Downloader;

use Silex\Application;

/**
 * Base controller
 * @author: Johann-S
 */
abstract class BaseController {
  protected $app;
  protected function __construct(Application $_app) {
      $this->app = $_app;
  }

  protected function render($template, array $vars = array()) {
    return $this->app['twig']->render($template, $vars);
  }
}
