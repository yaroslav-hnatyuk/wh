<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class RatingsController
{

    protected $ratingsService;

    public function __construct($service)
    {
        $this->ratingsService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->ratingsService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->ratingsService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->ratingsService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->ratingsService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->ratingsService->delete($id));
    }
}
