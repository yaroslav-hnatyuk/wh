<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class UsersController extends BaseController
{

    protected $usersService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->usersService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->usersService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->usersService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->usersService->save($request->request->all())
        );
    }

    public function saveCurrent(Request $request)
    {
        $userData = array(
            'id' => $this->getUser()->id,
            'first_name' => $request->request->get('first_name'),
            'last_name' => $request->request->get('last_name'),
            'email' => $request->request->get('email')
        );
        
        return new JsonResponse(
            $this->usersService->updatePersonalData($userData)
        );
    }

    public function update($id, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        return new JsonResponse(
            $this->usersService->update($id, $data['email'])
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->usersService->delete($id));
    }
}
