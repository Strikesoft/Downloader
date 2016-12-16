<?php
namespace Downloader\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Downloader\BaseController;

/**
 * Index controller
 * @author: Johann-S
 */
class IndexController extends BaseController
{
  public function __construct(Application $_app) {
      parent::__construct($_app);
  }

  public function indexAction() {
    return $this->render('index/index.twig');
  }
 }
