<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DishgroupsController
{

    protected $dishGroupService;

    public function __construct($service)
    {
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
