<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class SettingsController extends BaseController
{
    public function __construct(Application $app) 
    {
        $this->app = $app;
    }

    public function index()
    {
        return $this->app['twig']->render("settings/index.twig", array(
            'userRole' => $this->getUser()->role,
            'user' => $this->getUser(),
            'settings' => $this->app['settings.service']->getAll()
        ));
    }

}
