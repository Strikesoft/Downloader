<?php
namespace Downloader\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Downloader\BaseController;
use Downloader\Util\Downloader;

/**
 * Security controller
 * @author: Johann-S
 */
class SecurityController extends BaseController
{
    public function __construct(Application $_app) {
        parent::__construct($_app);
    }

    public function checkSecure() {
        return $this->app->json(array(
            'isSecure' => $this->app['passModalHash'] !== null
        ));
    }

    public function checkPassword(Request $request) {
        $pass = $request->request->get('password', null);
        if ($pass === null) {
            $pass = '';
        }
        return $this->app->json(array(
            'auth' => hash('sha256', $pass) === $this->app['passModalHash']
        ));
    }
}
