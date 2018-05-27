<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class CompaniesController
{

    protected $companiesService;

    public function __construct($service)
    {
        $this->companiesService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->companiesService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->companiesService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->companiesService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->companiesService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->companiesService->delete($id));
    }
}