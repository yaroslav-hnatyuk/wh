<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class OfficesController
{

    protected $officesService;

    public function __construct($service)
    {
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
