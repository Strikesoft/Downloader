<?php
namespace Downloader;

use Silex\Application as SilexApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Downloader\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

/**
 * Core of Downloader
 * @author: Johann-S
 */
class Application
{
  private $app;

  public function boot() {
    $this->app = new SilexApplication();
    $this->loadConfig();
    $this->app->register(new ServiceControllerServiceProvider());
    $this->app->register(new TwigServiceProvider(), array(
      'twig.path' => __DIR__ . '/../views',
    ));
    // Register monolog only if log = true in config file
    if (isset($this->app['log']) && $this->app['log']) {
      $this->app->register(new MonologServiceProvider(), array(
        'monolog.name' => 'Downloader',
        'monolog.logfile' => __DIR__ . '/../logs/downloader-' . date('Y-m-d') . '.log'
      ));
    }
    $router = new Router($this->app);
    $request = Request::createFromGlobals();
    $router->load();
    $this->app->run($request);
  }

  public static function log(SilexApplication $app, $type, $message, $vars = array()) {
    if (!isset($app['log']) || !$app['log']) {
      return;
    }
    switch($type) {
      case 'debug':
        $app['monolog']->addDebug($message, $vars);
        break;
      case 'info':
        $app['monolog']->addInfo($message, $vars);
        break;
      case 'notice':
        $app['monolog']->addNotice($message, $vars);
        break;
      case 'warning':
        $app['monolog']->addWarning($message, $vars);
        break;
      case 'error':
        $app['monolog']->addError($message, $vars);
        break;
      case 'critical':
        $app['monolog']->addCritical($message, $vars);
        break;
      case 'alert':
        $app['monolog']->addAlert($message, $vars);
        break;
      case 'emergency':
        $app['monolog']->addEmergency($message, $vars);
        break;
    }
  }

  private function loadConfig() {
    $config = Yaml::parse(file_get_contents(__DIR__ . '/../config/param.yml'));
    foreach ($config as $key => $value) {
      $this->app[$key] = $value;
    }
  }
}
