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

    public function getOne($id)
    {
        return new JsonResponse($this->dishGroupService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->dishGroupService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->dishGroupService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->dishGroupService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->dishGroupService->delete($id));
    }
}
