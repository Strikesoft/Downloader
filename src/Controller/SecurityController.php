<?php
namespace Downloader\Controller;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Downloader\BaseController;
use Downloader\Util\Downloader;
use Downloader\Application;

/**
 * Security controller
 * @author: Johann-S
 */
class SecurityController extends BaseController
{
    public function __construct(SilexApplication $_app) {
        parent::__construct($_app);
    }

    public function checkSecure() {
        return $this->app->json(array(
            'isSecure' => Application::isSecure()
        ));
    }

    public function checkPassword(Request $request) {
        $pass = $request->request->get('password', null);
        $passMatch = false;
        if ($pass === null) {
            $pass = '';
        }

        if (Application::isSecure()) {
            $passMatch = hash('sha256', $pass) === $this->app['passModalHash'];
            $this->app['session']->set('downloaderUser', array(
                'allowDownload' => $passMatch
            ));
        }
        return $this->app->json(array(
            'auth' => $passMatch
        ));
    }
}
