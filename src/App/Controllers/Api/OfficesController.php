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

    public function getOne($id)
    {
        return new JsonResponse($this->officesService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->officesService->getAll());
    }

    public function save(Request $request)
    {
        // hash('ripemd160', 'Наукова, 7d, Львів' . uniqid())
        return new JsonResponse(
            $this->officesService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->officesService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->officesService->delete($id));
    }
}
