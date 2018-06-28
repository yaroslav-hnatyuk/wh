<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class CompaniesController extends BaseController
{

    protected $companiesService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->companiesService = $service;
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('admin'));

        return new JsonResponse(
            $this->companiesService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $this->checkPermissions(array('admin'));
        
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->companiesService->update($data)
        );
    }

}
