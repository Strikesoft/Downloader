<?php
namespace Downloader\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController
{
  private $app;

  public function __construct(Application $_app)
  {
    $this->app = $_app;
  }

  public function indexAction() {
    
  }
 }
