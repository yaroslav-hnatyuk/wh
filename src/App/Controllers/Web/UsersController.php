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
        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');

        $stuff = $this->usersService->getAllWithCompaniesAndOffices(array(
            'company' => $company,
            'office' => $office
        ), true);
        $users = $this->usersService->getAllWithCompaniesAndOffices(array(
            'company' => $company,
            'office' => $office
        ), false, true);

        return $this->app['twig']->render("users/index.twig", array(
            'userRole' => $this->getUser()->role,
            'users' => $users,
            'stuff' => $stuff,
            'company' => $company,
            'office' => $office,
            'companies' => $this->app['companies.service']->getAll(),
            'offices' => $this->app['offices.service']->getAllByCompany($company)
        ));
    }
}
