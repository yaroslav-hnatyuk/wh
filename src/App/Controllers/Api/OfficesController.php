<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class OfficesController extends BaseController
{

    protected $officesService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->officesService = $service;
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('admin'));
        
        $offices = json_decode($request->getContent(), true);
        return new JsonResponse(
            $this->officesService->saveOffices($offices)
        );
    }
}
