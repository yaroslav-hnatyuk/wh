<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class CompaniesController extends BaseController
{
    private $companiesService;
    private $officesService;

    public function __construct(Application $app, $companiesService, $officesService) 
    {
        $this->app = $app;
        $this->companiesService = $companiesService;
        $this->officesService = $officesService;
    }

    public function index(Request $request)
    {
        $companies = $this->companiesService->getAllGroupedById();
        $offices = $this->officesService->getAll();
        foreach ($offices as $office) {
            $companies[$office['company_id']]['offices'][] = $office; 
        }

        return $this->app['twig']->render("companies/index.twig", array(
            'userRole' => $this->getUser()->role,
            'companies' => $companies,
            'host' => $request->getSchemeAndHttpHost()
        ));
    }
}
