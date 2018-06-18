<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class UsersController extends BaseController
{
    private $usersService;

    public function __construct(Application $app, $usersService) 
    {
        $this->app = $app;
        $this->usersService = $usersService;
    }

    public function index()
    {
        $stuff = $this->usersService->getAllWithCompaniesAndOffices(true);
        $users = $this->usersService->getAllWithCompaniesAndOffices(false, true);

        return $this->app['twig']->render("users/index.twig", array(
            'userRole' => $this->getUser()->role,
            'users' => $users,
            'stuff' => $stuff
        ));
    }
}
