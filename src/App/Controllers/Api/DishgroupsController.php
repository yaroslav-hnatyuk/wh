<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class DishgroupsController extends BaseController
{

    protected $dishGroupService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->dishGroupService = $service;
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('admin', 'manager'));

        $group = $request->request->all();
        $group['is_lunch'] = 0;
        $group['order'] = $this->dishGroupService->getMaxOrder() + 1;
        
        return new JsonResponse(
            $this->dishGroupService->save($group)
        );
    }

    public function update($id, Request $request)
    {
        $this->checkPermissions(array('admin', 'manager'));
        
        $data = $request->request->all();
        return new JsonResponse(
            $this->dishGroupService->update($data)
        );
    }

}
